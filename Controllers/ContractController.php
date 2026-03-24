<?php
require_once __DIR__ . '/../Models/Contract.php';

class ContractController
{
    private $contractModel;

    public function __construct($db)
    {
        $this->contractModel = new Contract($db);
    }

    public function download()
    {
        if (!isset($_SESSION['userId'])) {
            $this->redirect(basePath() . '/login');
            return;
        }

        $role = strtolower($_SESSION['userRole'] ?? '');
        if (!in_array($role, ['student', 'ngo'], true)) {
            $this->redirect(basePath() . '/dashboard');
            return;
        }

        $applicationId = (int) ($_GET['application_id'] ?? 0);
        if ($applicationId <= 0) {
            $_SESSION['flash_error'] = 'Invalid contract request.';
            $this->redirect(basePath() . '/dashboard');
            return;
        }

        $contractData = $this->contractModel->getContractByApplicationForUser($applicationId, (int) $_SESSION['userId'], $role);
        if (!$contractData) {
            $_SESSION['flash_error'] = $this->contractModel->getLastError() ?: 'Contract not available.';
            $this->redirect(basePath() . '/dashboard');
            return;
        }

        $pdfContent = $this->buildPdf($contractData);
        $safeContractNumber = preg_replace('/[^A-Za-z0-9_-]/', '-', $contractData['contract_number'] ?? 'contract');

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $safeContractNumber . '.pdf"');
        header('Content-Length: ' . strlen($pdfContent));
        echo $pdfContent;
        exit;
    }

    private function buildPdf($contractData)
    {
        $issuedOn = !empty($contractData['contract_created_at'])
            ? date('M d, Y', strtotime($contractData['contract_created_at']))
            : date('M d, Y');

        $deadline = !empty($contractData['project_deadline'])
            ? date('M d, Y', strtotime($contractData['project_deadline']))
            : 'N/A';

        $lines = [
            'PushForGood Volunteer Contract',
            'Contract Number: ' . ($contractData['contract_number'] ?? 'N/A'),
            'Issued On: ' . $issuedOn,
            '',
            'Project: ' . ($contractData['project_title'] ?? 'N/A'),
            'Project Deadline: ' . $deadline,
            '',
            'NGO Representative: ' . ($contractData['ngo_name'] ?? 'N/A') . ' (' . ($contractData['ngo_email'] ?? 'N/A') . ')',
            'Student Volunteer: ' . ($contractData['student_name'] ?? 'N/A') . ' (' . ($contractData['student_email'] ?? 'N/A') . ')',
            '',
            'Agreement Terms:',
            '1) NGO confirms acceptance of the student for the listed project.',
            '2) Student agrees to complete project tasks ethically and on time.',
            '3) Both parties agree to communicate through platform channels.',
            '4) This contract is generated automatically by PushForGood.',
            '',
            'Reference IDs:',
            'Application ID: ' . (int) ($contractData['application_id'] ?? 0),
            'Project ID: ' . (int) ($contractData['project_id'] ?? 0),
        ];

        return $this->renderSimplePdf($lines);
    }

    private function renderSimplePdf($lines)
    {
        $yStart = 790;
        $lineHeight = 16;

        $stream = "BT\n/F1 12 Tf\n50 " . $yStart . " Td\n";
        foreach ($lines as $index => $line) {
            $escaped = $this->escapePdfText($line);
            if ($index === 0) {
                $stream .= '(' . $escaped . ") Tj\n";
            } else {
                $stream .= '0 -' . $lineHeight . " Td\n(" . $escaped . ") Tj\n";
            }
        }
        $stream .= "ET";

        $objects = [];
        $objects[] = '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj';
        $objects[] = '2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj';
        $objects[] = '3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >> endobj';
        $objects[] = '4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj';
        $objects[] = '5 0 obj << /Length ' . strlen($stream) . " >> stream\n" . $stream . "\nendstream endobj";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $idx => $object) {
            $offsets[$idx + 1] = strlen($pdf);
            $pdf .= $object . "\n";
        }

        $xrefPos = strlen($pdf);
        $pdf .= 'xref' . "\n";
        $pdf .= '0 ' . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= 'trailer << /Size ' . (count($objects) + 1) . ' /Root 1 0 R >>' . "\n";
        $pdf .= 'startxref' . "\n" . $xrefPos . "\n";
        $pdf .= "%%EOF";

        return $pdf;
    }

    private function escapePdfText($text)
    {
        $text = str_replace('\\', '\\\\', (string) $text);
        $text = str_replace('(', '\\(', $text);
        $text = str_replace(')', '\\)', $text);
        return preg_replace('/[^\x20-\x7E]/', '?', $text);
    }

    private function redirect($location)
    {
        header('Location: ' . $location);
        exit;
    }
}
