<?php

namespace App\Helpers;

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\TcpdfFpdi;

class InvoicePDF extends TcpdfFpdi
{
    protected $template_p1 = null;
    protected $template_p2 = null;
    protected $font = 'msmincho';

    protected $colWidth= [
        'daily_report_date'=> 8.8, //月日
        'goods'=> 31.5, //品名
        'size'=> 5, //屯
        'quantity'=> 14, //数量
        'unit_price'=> 16.5, //単価
        'amount'=> 16, //金額
        'departure_point_name'=> 31, //発地
        'landing_name'=> 30.5, //着地
        'loading_fee'=> 13.5, //積込料
        'wholesale_fee'=> 13, //取卸料
        'incidental_fee'=> 13, //付帯業務料
        'waiting_fee'=> 13, //待機料
        'surcharge_fee'=> 13.5, //サーチャージ料
        'billing_fast_charge'=> 13, //通行料
        'delivery_destination'=> 35, //備考
        'staff_nm'=> 8.5, //号車
    ];

    protected $number_record_page_first = 22;
    protected $number_record_page_n = 42;
    protected $total_page = 1;

    function __construct(string $orientation = 'L', string $unit = 'mm', string $format = 'A4', bool $unicode = true, string $encoding = 'UTF-8', bool $diskcache = false, bool $pdfa = false)
    {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
        $this->template_p1 = config('params.invoice_pdf_template.page_1');
        $this->template_p2 = config('params.invoice_pdf_template.page_2');
        $fontname = \TCPDF_FONTS::addTTFfont(public_path('/fonts/MSMINCHO.TTF'), 'TrueTypeUnicode', '', 96);
    }

    protected function writePage(){
        $this->SetFont($this->font, '', 8);
        $this->SetXY(270, 4);
        $this->Cell(0, 7, $this->PageNo()."/".$this->total_page,0,0,'R');
    }

    public function writeHeader($content)
    {
        $this->setSourceFile($this->template_p1);
        $this->tplId = $this->importPage(1);

        $this->useImportedPage($this->tplId);
        $this->SetAutoPageBreak(true, 0);
        $this->writePage();
        $this->SetFont($this->font, '', 10);

        $this->SetXY(28, 16);
        $this->Write(8,'〒 '.$content->bill_zip_cd);
        $this->SetXY(28, 22);
        $this->MultiCell(82, 8, $content->bill_address,0,'L',false);

        $this->SetFont($this->font, 'B', 12);
        $this->SetXY(20, 35);
        $this->Write(8,$content->customer_nm);

        $this->SetFont($this->font, '', 10);
        $this->SetXY(98, 52);
        $this->Write(0,$content->customer_cd);

        $this->SetFontSize( 12);
        $this->SetXY(210, 25);
        $this->Cell(0, 10, TimeFunction::dateFormat($content->publication_date, 'Y年m月d日'),0,0,'R');

        $this->SetXY(211, 35);
        $this->Write(0,$content->business_office_nm);

        $this->SetFontSize( 10);
        $this->SetXY(215, 40);
        $this->MultiCell(18, 8, $content->zip_cd,0,'L',false);

        $this->SetXY(233, 40);
        $this->setCellHeightRatio(1);
        $this->MultiCell(0, 8, $content->address,0,'L',false);

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
        $this->Cell(29, 11, $content->total_fee,0,0,'R');
        $this->SetXY(156, 83);
        $this->Cell(29, 11, $content->consumption_tax,0,0,'R');
        $this->SetXY(185, 83);
        $this->Cell(34, 11, $content->tax_included_amount,0,0,'R');
    }

    public function getTotalPage($list){
        $numRecord = count($list);
        if($numRecord <= $this->number_record_page_first){
            $this->total_page = 1;
        }else{
            $this->total_page = ceil(($numRecord-$this->number_record_page_first)/$this->number_record_page_n) +1;
        }
    }

    public function openPagen(){
        $this->AddPage();
        $this->setSourceFile($this->template_p2);
        $this->tplId = $this->importPage(1);

        $this->useImportedPage($this->tplId);
        $this->SetAutoPageBreak(true, 0);
        $this->writePage();
        $this->SetFont($this->font, '', 8);
        $this->deleteLastRow();
    }

    public function deleteLastRow(){
        if($this->total_page > $this->PageNo()){
            $this->Rect(0,$this->PageNo()==1 ? 196.5 : 198.2,290,7, 'F',[],array(255,255,255));
        }
    }
    public function writeDetails($listContentDetails){
        $startX = 11;
        $startY = 99;
        $y = $startY;
        $h = 4.41;
        $sum = [
            'amount' => 0,
            'loading_fee' => 0,
            'wholesale_fee' => 0,
            'waiting_fee' => 0,
            'incidental_fee' => 0,
            'surcharge_fee' => 0,
            'billing_fast_charge' => 0,
        ];
        $count = 0;
        $this->SetFontSize( 8);
        $this->deleteLastRow();
        foreach ($listContentDetails as $detail){
            $sum['amount'] += floatval($detail->amount);
            $sum['loading_fee'] += $detail->loading_fee;
            $sum['wholesale_fee'] += $detail->wholesale_fee;
            $sum['waiting_fee'] += $detail->waiting_fee;
            $sum['incidental_fee'] += $detail->incidental_fee;
            $sum['surcharge_fee'] += $detail->surcharge_fee;
            $sum['billing_fast_charge'] += $detail->billing_fast_charge;
            $x = $startX;
            $y +=4.23;
            $this->setCellPaddings(1.000125,0,1.000125,0);
            foreach ($detail as $key => $value){
                switch ($key){
                    case  'departure_point_name':
                    case  'landing_name':
                    case  'delivery_destination':
                    case  'goods':
                        $align = 'L';
                        break;
                    case  'staff_nm':
                        $this->setCellPadding(0);
                        $align = 'L';
                        break;
                    case 'amount':
                    case 'loading_fee':
                    case 'wholesale_fee':
                    case 'waiting_fee':
                    case 'incidental_fee':
                    case 'surcharge_fee':
                    case 'billing_fast_charge':
                        $value = number_format($value);
                    default: $align ='R';
                }
                $w = $this->colWidth[$key];
                $this->SetXY($x, $y);
                $this->Cell($w, $h, $value,0,0,$align);
                $x +=$w;
            }
            $count++;
            if($this->PageNo()==1){
                if($count==$this->number_record_page_first && $this->PageNo()<$this->total_page){
                    $this->openPagen();
                    $count = 0;
                    $y =15.7;
                }
            }else{
                if($count==$this->number_record_page_n && $this->PageNo()<$this->total_page){
                    $this->openPagen();
                    $count = 0;
                    $y =15.7;
                }
            }
        }
        $this->writeTotal( $sum);

    }

    protected function writeTotal($sum){
        $this->setCellPaddings(1.000125,0,1.000125,0);
        $h = 4.6;
        $y = $this->PageNo()==1 ? 196.5 : 198.1;
        $this->SetXY(70.5, $y);
        $this->Cell(32, $h, number_format($sum['amount']),0,0,'R');
        $x = 164.5;
        $this->SetXY($x, $y);
        $this->Cell($this->colWidth['loading_fee'], $h, number_format($sum['loading_fee']),0,0,'R');
        $x +=$this->colWidth['loading_fee'];
        $this->SetXY($x, $y);
        $this->Cell($this->colWidth['wholesale_fee'], $h, number_format($sum['wholesale_fee']),0,0,'R');
        $x +=$this->colWidth['wholesale_fee'];
        $this->SetXY($x, $y);
        $this->Cell($this->colWidth['incidental_fee'], $h, number_format($sum['incidental_fee']),0,0,'R');
        $x +=$this->colWidth['incidental_fee'];
        $this->SetXY($x, $y);
        $this->Cell($this->colWidth['waiting_fee'], $h, number_format($sum['waiting_fee']),0,0,'R');
        $x +=$this->colWidth['waiting_fee'];
        $this->SetXY($x, $y);
        $this->Cell($this->colWidth['surcharge_fee'], $h, number_format($sum['surcharge_fee']),0,0,'R');
        $x +=$this->colWidth['surcharge_fee'];
        $this->SetXY($x, $y);
        $this->Cell($this->colWidth['billing_fast_charge'], $h, number_format($sum['billing_fast_charge']),0,0,'R');
    }


    public function createNewPdfFromExistedFile($path){
        $pageCount = $this->setSourceFile($path);
        for ($i = 1; $i <= $pageCount; $i++) {
            $this->tplId = $this->importPage($i,'/MediaBox');
            $this->AddPage();
            $this->useTemplate($this->tplId);
            $this->SetAutoPageBreak(true, 0);
            $this->SetFont($this->font, '', 10);
        }
    }
}