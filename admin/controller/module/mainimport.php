<?php
require_once DIR_SYSTEM . 'PHPExcel2/Classes/PHPExcel/IOFactory.php';

class chunkReadFilter implements PHPExcel_Reader_IReadFilter
{
    private $_startRow = 0;
    private $_endRow = 0;

    public function setRows($startRow, $chunkSize)
    {
        $this->_startRow = $startRow;
        $this->_endRow = $startRow + $chunkSize;
    }

    public function readCell($column, $row, $worksheetName = '')
    {
        if (($row == 1) || ($row >= $this->_startRow && $row < $this->_endRow)) {
            return true;
        }
        return false;
    }
}

class ControllerModuleMainimport extends Controller
{
    public function index()
    {
        $this->load->language('module/priceimport');
        $data['heading_title'] = $this->language->get('heading_title');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('module/mainimport');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/module', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/priceimport', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['action_import'] = $this->url->link('module/priceimport/import', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $data['action_import_continue'] = $this->url->link('module/priceimport/continueimport', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $data['action_export'] = $this->url->link('module/priceimport/export', 'user_token=' . $this->session->data['user_token'], 'SSL');

        $data['cancel'] = $this->url->link('extension/module&', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $data['form_link'] = $this->url->link('module/mainimport/loadfile', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $data['form_link_ajax'] = html_entity_decode($this->url->link('module/mainimport/readfile', 'user_token=' . $this->session->data['user_token'], 'SSL'));
        $data['form_link_gener_ajax'] = html_entity_decode($this->url->link('module/mainimport/gener', 'user_token=' . $this->session->data['user_token'], 'SSL'));

        //проверим есть ли файл
        $filename = DIR_CACHE . 'import.xlsx';
        if (file_exists($filename)) {
            $data['filenow'] = true;
        } else {
            $data['filenow'] = false;
        }

        $data['fields'] = $this->model_module_mainimport->getAllFields() ? $this->model_module_mainimport->getAllFields() : 0;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('module/mainimport', $data));
    }

    public function loadfile()
    {
        if (isset($_FILES["userfile"])) {
            if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
                //Returns TRUE if filename was uploaded using HTTP POST
                $filename = 'import.xlsx';
                //basename -- Returns the file name from the specified path
                $uploadfile = DIR_CACHE . $filename;
                move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile);
            }

            $this->readfile();

            $this->response->redirect($this->url->link('module/mainimport', 'user_token=' . $this->session->data['user_token']));
        }
    }

    public function readfile()
    {
        $uploadfile = DIR_CACHE . 'import.xlsx';
        $objPHPExcel = PHPExcel_IOFactory::load($uploadfile);
        $this->load->model('module/mainimport');
        $massHeaders = array();
        $massDate = array();
        $razdel = 42;

        $worksheetCount = 0;

      foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

            $highestRow = $worksheet->getHighestRow(); // e.g. 10
            $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

            if ($worksheetCount == 0) {
                for ($row = 1; $row <= $highestRow; ++$row) {
                    if ($row > 1) {
                        $massDate[$row] = array();
                    }

                    $tempMass = array();
                    $name = array();
                    $categories = array();
                    $countries = array();
                    $sizeunit = array();
                    $volumeunit = array();
                    $features = array();

                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                        $cell = $worksheet->getCellByColumnAndRow($col, $row);
                        $val = $cell->getValue();

                        // Защита от лишних пустых строк в документе
                        if ($col == 0 && !$val) {
                            break 3;
                        }
                        if ($col == 2 && !$val) {
                            break;
                        }
                        if ($row == 1) {
                            if ($val) {
                                array_push($massHeaders, $val);
                            }
                        } else {
                            if ($col < $razdel) {
                                //Hardcoded data area

                                switch ($col) {
                                    case 1:
                                        if($val==''){
                                            $val=10000;
                                        }
                                        $massDate[$row]['sort'] = $val;
                                        break;
                                        case 2:
                                        $massDate[$row]['1c_id'] = $val;
                                        break;
                                    case 6:
                                        $massDate[$row]['images'] = $val;
                                        break;
                                    case 7:
                                        $name[2] = $val;
                                        break;
                                    case 8:
                                        $name[3] = $val;
                                        break;
                                    case 9:
                                        $name[1] = $val;
                                        break;
                                    case 10:
                                        $massDate[$row]['artikul'] = $val;
                                        break;
                                    case 11:
                                        $categories[0][2] = $val;
                                        break;
                                    case 12:
                                        $categories[0][3] = $val;
                                        break;
                                    case 13:
                                        $categories[0][1] = $val;
                                        break;
                                    case 14:
                                        $categories[1][2] = $val;
                                        break;
                                    case 15:
                                        $categories[1][3] = $val;
                                        break;
                                    case 16:
                                        $categories[1][1] = $val;
                                        break;
                                    case 17:
                                        $categories[2][2] = $val;
                                        break;
                                    case 18:
                                        $categories[2][3] = $val;
                                        break;
                                    case 19:
                                        $categories[2][1] = $val;
                                        break;
                                    case 20:
                                        $massDate[$row]['manufacture'] = trim($val);
                                        break;
                                    case 21:
                                        $massDate[$row]['collection'] = trim($val);
                                        $massDate[$row]['name_collection'] = trim($val);
                                        break;
                                    case 22:
                                        $countries[2] = $val;
                                        break;
                                    case 23:
                                        $countries[3] = $val;
                                        break;
                                    case 24:
                                        $countries[1] = $val;
                                        break;
                                    case 27:
                                        $massDate[$row]['length'] = $val;
                                        break;
                                    case 28:
                                        $massDate[$row]['width'] = $val;
                                        break;
                                    case 29:
                                        $massDate[$row]['height'] = $val;
                                        break;
                                    case 30:
                                        $massDate[$row]['diameter'] = $val;
                                        break;
                                    case 31:
                                        $massDate[$row]['weight'] = $val;
                                        break;
                                    case 32:
                                        $massDate[$row]['volume'] = $val;
                                        break;
                                    case 33:
                                        $sizeunit[2] = $val;
                                        break;
                                    case 34:
                                        $sizeunit[3] = $val;
                                        break;
                                    case 35:
                                        $sizeunit[1] = $val;
                                        break;
                                    case 36:
                                        $volumeunit[2] = $val;
                                        break;
                                    case 37:
                                        $volumeunit[3] = $val;
                                        break;
                                    case 38:
                                        $volumeunit[1] = $val;
                                        break;
                                    case 39:
                                        $features[2] = $val;
                                        break;
                                    case 40:
                                        $features[3] = $val;
                                        break;
                                    case 41:
                                        $features[1] = $val;
                                        break;
                                }
                            } else {
                                if (isset($massHeaders[$col])) {
                                    $tempMass[$massHeaders[$col]] = $val;
                                }
                            }
                        }
                    }

                    $attributes = array();
                    $nnn='';
                    $lineRazdel = 1;
                    if ($tempMass) {
                        foreach ($tempMass as $key => $tm) {
                            $razdelNow = $lineRazdel % 3;
                            if ($razdelNow == 1) {
                                $nnn = $key;
                                if ($tm) {
                                    $attributes[$key][2] = $tm;
                                }
                            } else if ($razdelNow == 2) {
                                if ($tm) {
                                    $attributes[$nnn][3] = $tm;
                                }
                            } else if ($razdelNow == 0) {
                                if ($tm) {
                                    $attributes[$nnn][1] = $tm;
                                }
                            }
                            $lineRazdel++;
                        }
                    }

                    if ($row > 1) {

                        $massDate[$row]['name'] = str_replace('\n', '', json_encode( $name, JSON_UNESCAPED_UNICODE));
                        $massDate[$row]['categories'] = str_replace('\n', '', json_encode($categories, JSON_UNESCAPED_UNICODE));
                        $massDate[$row]['countries'] = str_replace('\n', '', json_encode($countries, JSON_UNESCAPED_UNICODE));
                        $massDate[$row]['sizeunit'] = str_replace('\n', '', json_encode($sizeunit, JSON_UNESCAPED_UNICODE));
                        $massDate[$row]['volumeunit'] = str_replace('\n', '', json_encode($volumeunit, JSON_UNESCAPED_UNICODE));
                        $massDate[$row]['features'] = json_encode($features, JSON_UNESCAPED_UNICODE);
                        $new_mass_attr=array();
                        foreach ($attributes as $key=>$atrs){
                           // $this->log->write($key);

                            foreach ($atrs as $keys=>$atr){

                                $new_mass_attr[$key][$keys][]=explode(',',$atr);
                            }


                        }
                        $massDate[$row]['new_mass_attr']=json_encode($new_mass_attr, JSON_UNESCAPED_UNICODE);;
                      //  $this->log->write($new_mass_attr);

                        $massDate[$row]['attr'] = json_encode($attributes, JSON_UNESCAPED_UNICODE);
                    }
                }

                $worksheetCount++;
            } else {
                break;
            }
        }

        $this->load->model('module/mainimport');
        foreach ($massDate as $md) {
            if ($md) {
               $this->model_module_mainimport->setProduct($md);
            }
        }

        unlink($uploadfile);
    }

    public function gener()
    {
        $this->load->model('module/mainimport');
        $mass = $this->model_module_mainimport->getFields();



        foreach ($mass as $value) {
            // Картинки
            $imagesMass = array();
            $images = $pieces = explode(",", $value['images']);
            if ($images) {
                foreach ($images as $image) {
                    $imagesMass[] = 'catalog/products/' . trim($image);
                }
            }
            $value['images'] = $imagesMass;
            //$this->log->write($value['manufacture']);
            $value['manufacture'] = $this->model_module_mainimport->getManufactureID($value['manufacture']);
           // $this->log->write($value['manufacture']);
            $cat_array =json_decode($value['categories'], JSON_UNESCAPED_UNICODE);
            // Потом нужно разобраться с полем collection
            $value['name_collect']= $value['collection'];

            $value['collection'] = $this->model_module_mainimport->getCollectionId($value['collection'],$value['manufacture'],$cat_array);

       // $this->log->write($value['collection']);

           if(!empty($value['collection'])){
               $value['collection'] =  $this->model_module_mainimport->setCollections($value['collection'],$cat_array,$value['manufacture'],trim($value['name_collect']));
               $value['collection'] =  $this->model_module_mainimport->setCollectionItem($value['collection'],$cat_array,$value['manufacture'],trim($value['name_collect']));
        }


            // Size attributes
            $attrs = array();

            $sizeunit = json_decode($value['sizeunit'], JSON_UNESCAPED_UNICODE);
            unset($value['sizeunit']);
            $volumeunit = json_decode($value['volumeunit'], JSON_UNESCAPED_UNICODE);
            unset($value['volumeunit']);

            if ($value['length']) {
                $attribute_id = $this->model_module_mainimport->getAttributeID('длина');
                foreach ($sizeunit as $key => $size) {
                    $length[$key] = round($value['length'],2) . ' ' . $size;
                }
                $attrs[$attribute_id] = $length;
                unset($value['length'] );
            }

            if ($value['width']) {
                $attribute_id = $this->model_module_mainimport->getAttributeID('ширина');
                foreach ($sizeunit as $key => $size) {
                    $width[$key] = round($value['width'],2) . ' ' . $size;
                }
                $attrs[$attribute_id] = $width;
                unset($value['width']);
            }

            if ($value['height']) {
                $attribute_id = $this->model_module_mainimport->getAttributeID('высота');
                foreach ($sizeunit as $key => $size) {
                    $height[$key] = round($value['height'],2) . ' ' . $size;
                }
                $attrs[$attribute_id] = $height;
                unset($value['height']);
            }

            if ($value['diameter']) {
                $attribute_id = $this->model_module_mainimport->getAttributeID('диаметр');
                foreach ($sizeunit as $key => $size) {
                    $diameter[$key] = round($value['diameter'],2) . ' ' . $size;
                }
                $attrs[$attribute_id] = $diameter;
                unset($value['diameter']);
            }

            if ($value['weight']) {
                $attribute_id = $this->model_module_mainimport->getAttributeID('вес');
                $weight[1] = round($value['weight'],2) . ' kg';
                $weight[2] = round($value['weight'],2) . ' кг';
                $weight[3] = round($value['weight'],2) . ' кг';
                $attrs[$attribute_id] = $weight;
                unset($value['weight']);
            }

            if ($value['volume']) {
                $attribute_id = $this->model_module_mainimport->getAttributeID('объем');
                foreach ($volumeunit as $key => $size) {
                    $volume[$key] = round($value['volume'],2) . ' ' . $size;
                }
                $attrs[$attribute_id] = $volume;
                unset($value['volume']);
            }

            // END of Size Attributes

            $value['name'] = json_decode($value['name'], JSON_UNESCAPED_UNICODE);


            $categories = json_decode($value['categories'], JSON_UNESCAPED_UNICODE);
            $catRezult = array();
            $lvl = 0;
            $parent = 0;

            $additionCategory = array(
                '1' => 'Store',
                '2' => 'Магазин',
                '3' => 'Магазин'
            );


            // Hardkod for Main Category "Store"
            array_unshift($categories, $additionCategory);



            foreach ($categories as $category) {
                $category = (array)$category;

                if (isset($category[2]) && $category[2]) {

                    $parent = $this->model_module_mainimport->getCategory($category[2], $parent, $lvl,$category);
                    $catRezult[] = $parent;
                } else {
                    break;
                }
                $lvl++;
            }
            $value['categories'] = $catRezult;



            // Сountries
            $attribute_id = $this->model_module_mainimport->getAttributeID('страна');
            $countriesDecod = json_decode($value['countries'], JSON_UNESCAPED_UNICODE);
            foreach ($countriesDecod as $key => $size) {
                $countries[$key] = $size;
            }
            $attrs[$attribute_id] = $countries;
            unset($value['countries']);

            // Description
            $value['features'] = json_decode($value['features'], JSON_UNESCAPED_UNICODE);

            // Other attributes
            $attrsDecod = json_decode($value['attr'], JSON_UNESCAPED_UNICODE);
            $attrsDecod2 = json_decode($value['attr2'], JSON_UNESCAPED_UNICODE);



            foreach ($attrsDecod as $key => $val) {
                $attribute_id = $this->model_module_mainimport->getAttributeID($key);
                foreach ($val as $lang => $dataAttr) {
                    $attrs[$attribute_id][$lang] = $dataAttr;
                }

            }

            foreach ($attrsDecod2 as $key => $val) {
                $attribute_id = $this->model_module_mainimport->getAttributeID($key);

                foreach ($val as $lang => $dataAttr) {
                    $attrs2[$attribute_id][$lang] = $dataAttr;

                }

            }

            $value['attr'] = $attrs;
            $value['attr2'] = $attrs2;


            if(!isset($value['sort'])||$value['sort']==''){
                $value['sort']=0;
            }

            // 1 - get the product id from the table product_to_1c
            $product_id = $this->model_module_mainimport->getProdIdById1c($value['1c_id']);

            if ($product_id) {
                $this->model_module_mainimport->updateProduct($product_id, $value);
            } else {
                $this->model_module_mainimport->addProduct($value);
            }

            // Let's clean up the record from the temporary table
            $this->model_module_mainimport->delTempProd($value['optprod_id']);
        }

        echo 'SUCCESS';
    }
}
