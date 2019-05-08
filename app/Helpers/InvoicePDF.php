<?php

namespace App\Helpers;

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\TcpdfFpdi;

class InvoicePDF extends TcpdfFpdi
{
    protected $template_p1 = null;
    protected $template_p2 = null;
    protected $font = 'cid0jp';
    function __construct(string $orientation = 'L', string $unit = 'mm', string $format = 'A4', bool $unicode = true, string $encoding = 'UTF-8', bool $diskcache = false, bool $pdfa = false)
    {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
        $this->template_p1 = config('params.invoice_pdf_template.page_1');
        $this->template_p2 = config('params.invoice_pdf_template.page_2');
    }

    public function writeHeader($content)
    {
        $this->setSourceFile($this->template_p1);
        $this->tplId = $this->importPage(1);

        $this->useImportedPage($this->tplId);

        $this->SetFont($this->font, '', 10);
        $this->SetXY(28, 16);
        $this->Write(8,'〒'.$content->bill_zip_cd);
        $this->SetXY(28, 22);
        $this->Write(8,$content->bill_address);

        $this->SetFont($this->font, 'B', 12);
        $this->SetXY(20, 35);
        $this->Write(8,$content->bill_address);

        $this->SetFont($this->font, '', 10);
        $this->SetXY(98, 52);
        $this->Write(0,$content->customer_cd);

        $this->SetFontSize( 12);
        $this->SetXY(210, 25);
        $this->Cell(0, 10, TimeFunction::getTimestamp('Y年m月d日'),1,0,'R');

        $this->SetXY(210, 40);
        $this->Write(0,$content->business_office_nm);

        $this->SetFontSize( 10);
        $this->SetXY(215, 45);
        $this->Write(0,$content->address);

        $this->SetFontSize( 9);
        $this->SetXY(220, 49);
        $this->Write(0,$content->phone_number);
        $this->SetXY(220, 53);
        $this->Write(0,$content->fax_number);


        $this->SetFontSize( 10);
        $this->SetXY(11, 83);
        $this->Cell(29, 11, $content->sales_amount,0,0,'R');
        $this->SetXY(40, 83);
        $this->Cell(29, 11, $content->incidental_other,0,0,'R');
        $this->SetXY(69, 83);
        $this->Cell(29, 11, $content->surcharge_fee,0,0,'R');
        $this->SetXY(98, 83);
        $this->Cell(29, 11, $content->toll_fee,0,0,'R');
        $this->SetXY(127, 83);
        $this->Cell(29, 11, '',0,0,'R');
        $this->SetXY(156, 83);
        $this->Cell(29, 11, $content->consumption_tax,0,0,'R');                  $this->SetXY(185, 83);
        $this->Cell(34, 11, $content->tax_included_amount,0,0,'R');

//        $this->SetTextColor(0);
//        $this->SetXY(PDF_MARGIN_LEFT, 5);
//        $this->Cell(0, $size['height'], 'TCPDF and FPDI');
    }

}