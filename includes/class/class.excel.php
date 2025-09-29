<?php

    class Excel {

        protected $objFile;
        protected $objData;

        function __construct(){
            include dirname(__FILE__).'/lib/PHPExcel.php';

        }

        public function readFile($path="") {

            if(!$path)
                return false;

            $this->objFile = PHPExcel_IOFactory::identify($path);
            $this->objData = PHPExcel_IOFactory::createReader($this->objFile);


            $this->objData->setReadDataOnly(true);

            $objPHPExcel = $this->objData->load($path);

            $sheet = $objPHPExcel->setActiveSheetIndex(0);

            $Totalrow = $sheet->getHighestRow();
            $LastColumn = $sheet->getHighestColumn();

            $TotalCol = PHPExcel_Cell::columnIndexFromString($LastColumn);

            $data = [];
            for ($i = 2; $i <= $Totalrow; $i++) {
                for ($j = 0; $j < $TotalCol; $j++) {
                    $data[$i - 2][$j] = $sheet->getCellByColumnAndRow($j, $i)->getValue();
                }
            }

            return $data;
        }

        public function convert_status_viettel($status="",$revese=false){
            if($revese == false){
                $status = preg_replace("#Giao thành công#si", "approved", $status);
                $status = preg_replace("#Hoàn thành công#si", "shiperror", $status);
                $status = preg_replace("#(Đang vận chuyển|Đang giao hàng)#si", "shipping", $status);
            } else {
                $status = preg_replace("#approved#si", "Giao thành công", $status);
                $status = preg_replace("#shiperror#si", "Hoàn thành công", $status);
                $status = preg_replace("#shipping#si", "Đang vận chuyển", $status);
            }

            return $status;
        }

        public function import_viettel($path="",$status=""){

            if(!$path)
                return false;

            $data = $this->readFile($path);

            $results = array();
            foreach ($data as $col) {
                if($col[0] && $col[1]){ // && $col[2] && is_numeric($col[2])

                    $id = ($col[2] && is_numeric($col[2]) ? trim($col[2]) : (preg_match("#^QTN19([0-9]+)$#si", trim($col[1])) ? trim($col[1]) : '') );
                    if($status){
                        if(preg_match("#".$this->convert_status_viettel($status,true)."#si", $col[5]))
                            $results[] = array(
                                            "id" => preg_replace("#^QTN19([0-9]+)$#si", "$1", $id),
                                            "status" => $this->convert_status_viettel($col[5]),
                                            "status_text" => $col[5],
                                            "name" => $col[8],
                                            "price" => $col[9],
                                            "order_name" => $col[16],
                                            "order_phone" => $col[18],
                                            "order_address" => $col[17]
                                        );
                    } else
                        $results[] = array(
                                        "id" => preg_replace("#^QTN19([0-9]+)$#si", "$1", $id),
                                        "status" => $this->convert_status_viettel($col[5]),
                                        "status_text" => $col[5],
                                        "name" => $col[8],
                                        "price" => $col[9],
                                        "order_name" => $col[16],
                                        "order_phone" => $col[18],
                                        "order_address" => $col[17]
                                    );
                }
            }

            return $results;
        }

        public function export_postback($data=array()){
            if(!$data)
                return false;
            $title = array("STT","Offer","Landing Page","Date","Type","State","Request url","Response code");
            $excel = new PHPExcel();
            $excel->setActiveSheetIndex(0);
            $excel->getActiveSheet()->setTitle('List Postback');

            $style = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_JUSTIFY
                )
            );

            $excel->getDefaultStyle()->applyFromArray($style);

            $excel->getActiveSheet()->getRowDimension(7)->setRowHeight(31);
            $excel->getActiveSheet()->getStyle('A7:H7')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('c5e0b3');

            //Xét in đậm cho khoảng cột
            $excel->getActiveSheet()->getStyle('A7:H7')->getFont()->setBold(true);

            $i = 0;
            foreach ($title as $t) {
                $excel->getActiveSheet()->setCellValueByColumnAndRow($i, 7, $t);
                $i++;
            }


            $row = 8;
            foreach ($data as $d) {
                $col = 0;
                foreach($d as $value) {
                    $excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
                    $col++;
                }
                $excel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);
                $row++;
            }

            foreach(range('A','H') as $columnID) {
                $excel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }

            $save = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $save->save('php://output');
        }

        public function export_viettel($data=array()) {

            if(!$data)
                return false;
            $title = array("STT","Mã đơn hàng","Tên người nhận","Số ĐT người nhận","Địa chỉ nhận","Tỉnh đến","Quận/Huyện đến","Tên hàng hóa","Số lượng","Trọng lượng (gram)","Giá trị hàng (VND)","Tiền thu hộ COD (VND)","Dịch vụ","Dịch vụ cộng thêm","Người trả cước","Ghi chú","Thời gian giao","Caller","Note","Đơn trùng");
            $excel = new PHPExcel();
            $excel->setActiveSheetIndex(0);
            $excel->getActiveSheet()->setTitle('Danh sách đơn hàng');

            $style = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_JUSTIFY
                )
            );

            $excel->getDefaultStyle()->applyFromArray($style);

            $excel->getActiveSheet()->getRowDimension(7)->setRowHeight(31);
            $excel->getActiveSheet()->getStyle('A7:T7')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('c5e0b3');

            //Xét in đậm cho khoảng cột
            $excel->getActiveSheet()->getStyle('A7:T7')->getFont()->setBold(true);

            $i = 0;
            foreach ($title as $t) {
                $excel->getActiveSheet()->setCellValueByColumnAndRow($i, 7, $t);
                $i++;
            }


            $row = 8;
            foreach ($data as $d) {
                $col = 0;
                foreach($d as $value) {
                    $excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
                    $col++;
                }
                $excel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);
                $row++;
            }

            foreach(range('A','T') as $columnID) {
                $excel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }

            $save = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $save->save('php://output');



        }


        public function export_vnpost($data=array()) {

            if(!$data)
                return false;

            $title = array("STT","Tên người nhận (*)","Điện thoại nhận (*)","SỐ NHÀ - TÊN ĐƯỜNG/PHỐ NGƯỜI NHẬN (*)","PHƯỜNG/XÃ - QUẬN/HUYỆN - TỈNH/THÀNH PHỐ NGƯỜI NHẬN (*)","DỊCH VỤ (*)","CHO XEM HÀNG","HÌNH THỨC THU GOM (*)","KHỐI LƯỢNG (GAM) (*)","CHỈ DẪN PHÁT","NỘI DUNG","TIỀN THU HỘ - COD (VNĐ)","DỊCH VỤ KHAI GIÁ (VNĐ)","DỊCH VỤ HÓA ĐƠN","DỊCH VỤ BÁO PHÁT (AR)","CỘNG THÊM CƯỚC VÀO TIỀN THU HỘ","MÃ ĐƠN HÀNG","Caller","Note","Đơn trùng");


            $excel = new PHPExcel();
            $excel->setActiveSheetIndex(0);
            $excel->getActiveSheet()->setTitle('Danh sách đơn hàng');

            $style = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_JUSTIFY
                )
            );

            $excel->getDefaultStyle()->applyFromArray($style);

            $excel->getActiveSheet()->getRowDimension(8)->setRowHeight(31);
            $excel->getActiveSheet()->getStyle('A8:T8')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('c5e0b3');

            //Xét in đậm cho khoảng cột
            $excel->getActiveSheet()->getStyle('A8:T8')->getFont()->setBold(true);

            $i = 0;
            foreach ($title as $t) {
                $excel->getActiveSheet()->setCellValueByColumnAndRow($i, 8, $t);
                $i++;
            }


            $row = 9;
            foreach ($data as $d) {
                $col = 0;
                foreach($d as $value) {
                    $excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
                    $col++;
                }
                $excel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);
                $row++;
            }

            foreach(range('A','T') as $columnID) {
                $excel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }

            $save = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $save->save('php://output');



        }

    }


?>