<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Importexport\Controller;
/**
 * 导出方法
 */
class ExportController{
    public $Excel;
    public $objectExcel;
	public function __construct(){
        require_once APP_PATH.'../PHPExcel/PHPExcel.php';
        require_once APP_PATH.'../PHPExcel/PHPExcel/IOFactory.php';
        $this->excel = new \PHPExcel();
	}

    //首页
	public function index($data) {

	}
	public function getExcelInfo(){

    }
    public function getSheetCount($inputFileName){
        $sheetCount = $this->objPHPExcel->getSheetCount($inputFileName);
        return $sheetCount;
    }
    public function loadSheetAll($inputFileName){
        $inputFileType = \PHPExcel_IOFactory::identify($inputFileName);
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $objReader->setLoadAllSheets();
        //$objReader ->setReadDataOnly(true); //只读取数据,会智能忽略所有空白行,这点很重要！！！
        $objPHPExcel = $objReader->load($inputFileName);
        $this->objectExcel = $objPHPExcel;
    }
    public function loadSheetOnly($inputFileName,$sheetname){
        $inputFileType = \PHPExcel_IOFactory::identify($inputFileName);
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $objReader->setLoadSheetsOnly($sheetname);
        //$objReader ->setReadDataOnly(true); //只读取数据,会智能忽略所有空白行,这点很重要！！！
        $objPHPExcel = $objReader->load($inputFileName);
        $this->objectExcel = $objPHPExcel;

    }
    public function getSheetNames(){
        return $this->objectExcel->getSheetNames();
    }
    public function getSheetOnly(){

    }

	public function rankings(){
        /*获取当前时间*/
        $date = I('date') ?: date('Y-m-d',time());
        /*获取上周一*/
        $startWeek = strtotime('-1 sunday -6day',strtotime($date));
        /*获取上周日*/
        $endWeek = strtotime('-1 sunday',strtotime($date)) ;
        $inputFileName = './test/phban.xlsx';
        date_default_timezone_set(‘PRC’);
        $this->loadSheetAll($inputFileName);
        $sheetNames = $this->getSheetNames();
        echo "<pre>";
        print_r($sheetNames);
        exit;

        /* 添加sheet，然后往里面添加数据 */
        $this->objectExcel->createSheet()->setTitle('test-sheet')
            ->setCellValue('A1', '你好')
            ->setCellValue('B2', '世界')
            ->setCellValue('C1', '你好')
            ->setCellValue('D2', '世界');

        $objWriter = \PHPExcel_IOFactory::createWriter($this->objectExcel,'Excel2007'); //设定写入excel的类型
        $objWriter->save('./test/phban.xlsx');

        echo "<pre>";
        print_r($this->getSheetNames());
        exit;


        $sheet = $objPHPExcel->getSheet(1);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        for ($row = 1; $row <= $highestRow; $row++){
            // Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            //这里得到的rowData都是一行的数据，得到数据后自行处理，我们这里只打出来看看效果
            var_dump($rowData);
            echo "<br />";
        }
        exit;

        $sheet = $objPHPExcel->getSheet('820-826');
        echo "<pre>";
        print_r($sheet);
        exit;
    }

}


