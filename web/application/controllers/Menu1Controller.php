<?php

/**
 * Description of Menu1
 *
 * @author spondbob
 */
class Menu1Controller extends CI_Controller {

    private $activeUser = null;

    public function __construct() {
        parent::__construct();
        $this->load->library('layout', array('controller' => 'menu1'));
        $this->load->library(array('LibMenu1', 'LibArea'));
        $this->load->helper(array('form'));
        $this->activeUser = $this->libuser->activeUser;
        $this->_accessRules();
    }

    /*
     * array(1, 2, ...)
     * array('*')
     * array(
     *  'index' => array('*')
     *  'menu'  => array('@')
     * )
     * array(
     *  'index' => array(1, 2, ...)
     *  'menu'  => array(3, 4, ...)
     * )
     */

    private function _accessRules() {
        $access = new AccessRule();
        $access->activeRole = $this->activeUser['role'];
        $access->allowedRoles = array(1, 3);
        $access->validate();
    }

    public function index() {
        $this->view();
    }

    public function view() {
        $fArea = (isset($_GET['area']) ? $_GET['area'] : 0);
        $fDaya = (isset($_GET['daya']) ? $_GET['daya'] : 0);
        $fTglPasang = (isset($_GET['tglPasang']) ? $_GET['tglPasang'] : 0);
        $data = array(
            'pageTitle' => 'Menu 1',
            'data' => array(),
            'label' => $this->dil->attributeLabels(),
            'sAjaxSource' => site_url("menu1/data?area=".$fArea."&daya=".$fDaya.'&tglPasang='.$fTglPasang),
        );

        $data['dropdownData'] = array(
            'area' => array(
                'data' => $this->libarea->getList(),
                'selected' => $fArea,
            ),
            'daya' => array(
                'data' => $this->libmenu1->getListRangeDaya(),
                'selected' => $fDaya,
            ),
            'tglPasang' => array(
                'data' => $this->libmenu1->getListRangeTglPasang(),
                'selected' => $fTglPasang,
            ),
        );

        /*
          if (isset($_GET['submit'])) {
          $filter = array(
          'area' => $_GET['area'],
          'daya' => $_GET['daya'],
          'tglPasang' => $_GET['tglPasang'],
          'limit' => 10,
          'offset' => 0,
          );
          $d = $this->libmenu1->filter($filter);
          $data['data'] = $d['data'];
          }
         * 
         */
        $data['sidebar']['dropdownData'] = $data['dropdownData'];
        $this->layout->render('main', $data);
    }

    public function data() {
        $filter = array(
            'area' => (isset($_GET['area']) ? $_GET['area'] : -1),
            'daya' => (isset($_GET['daya']) ? $_GET['daya'] : -1),
            'tglPasang' => (isset($_GET['tglPasang']) ? $_GET['tglPasang'] : -1 ),
            'limit' => (isset($_GET['iDisplayLength']) && $_GET['iDisplayLength'] != -1 ? intval($_GET['iDisplayLength']) : 50),
            'offset' => (isset($_GET['iDisplayStart']) ? intval($_GET['iDisplayStart']) : 0),
        );
        $data = $this->libmenu1->filter($filter);
        $aaData = array();
        foreach ($data['data'] as $d) {
            $aaData[] = array(
                $d->IDPEL,
                $d->NAMA,
                ($d->JENIS_MK == "A" ? "AMR" : ($d->JENIS_MK == "E" ? "Elektronik" : ($d->JENIS_MK == "M" ? "Mekanik" : "Blank"))),
                $d->KDGARDU,
                $d->NOTIANG
            );
        }
        $output = array(
            "sEcho" => (isset($_GET['sEcho']) ? intval($_GET['sEcho']) : 1),
            "iTotalRecords" => $data['num'],
            "iTotalDisplayRecords" => $data['num'],
                //"aaData" => $data['data']
        );
        $output['aaData'] = $aaData;
        echo json_encode($output);
    }
    
    public function export(){
        $this->load->library('PHPExcel');
        $filter = array(
            'area' => (isset($_GET['area']) ? $_GET['area'] : -1),
            'daya' => (isset($_GET['daya']) ? $_GET['daya'] : -1),
            'tglPasang' => (isset($_GET['tglPasang']) ? $_GET['tglPasang'] : -1 ),
        );
        //$objPHPExcel = $this->libmenu1->export($filter);
        $label = $this->dil->attributeLabels();

        $daya = $this->libmenu1->getListRangeDaya(true);
        $tglPasang = $this->libmenu1->getListRangeTglPasang(true);

        $filter['limit'] = -1;
        $filter['offset'] = -1;
        $filter['select'] = (!array_key_exists('select', $filter) ? 'IDPEL,NAMA,JENIS_MK,KDGARDU,NOTIANG' : $filter['select']);
        $filter['daya'] = $daya[$filter['daya']];
        $filter['tglPasang'] = $tglPasang[$filter['tglPasang']];

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("spondbob")
                ->setLastModifiedBy("spondbob")
                ->setTitle("Office 2007 XLSX")
                ->setSubject("Office 2007 XLSX")
                ->setDescription("Schematics2011")
                ->setKeywords("schematics")
                ->setCategory("file");
        $objPHPExcel->getSheet(0)->setTitle('Menu 1');
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', $label['IDPEL'])
                ->setCellValue('B1', $label['NAMA'])
                ->setCellValue('C1', $label['JENIS_MK'])
                ->setCellValue('D1', $label['KDGARDU'])
                ->setCellValue('E1', $label['NOTIANG']);
        $i = 2;
        $d = $this->dil->filterMenu1($filter, date('Y'));
        foreach ($d['data'] as $r) {
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $r->IDPEL)
                    ->setCellValue('B' . $i, $r->NAMA)
                    ->setCellValue('C' . $i, ($r->JENIS_MK == "A" ? "AMR" : ($r->JENIS_MK == "E" ? "Elektronik" : ($r->JENIS_MK == "M" ? "Mekanik" : "Blank"))))
                    ->setCellValue('D' . $i, $r->KDGARDU)
                    ->setCellValue('E' . $i, $r->NOTIANG);
            $i++;
        }
        $objPHPExcel->getActiveSheet()->setTitle('Menu 1 - PLN Watch');

        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(str_replace('.php', '.xlsx', __FILE__));
    }

}

?>
