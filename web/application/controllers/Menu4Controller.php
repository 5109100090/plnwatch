<?php

/**
 * Description of Menu4
 *
 * @author spondbob
 */
class Menu4Controller extends CI_Controller {

    private $controller = 'Menu4';
    private $activeUser = null;

    public function __construct() {
        parent::__construct();
        $this->load->library('layout', array('controller' => strtolower($this->controller)));
        $this->load->library(array('LibMenu4', 'LibArea'));
        $this->load->helper(array('form'));
        $this->activeUser = $this->libuser->activeUser;
        $this->_accessRules();
    }

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
        $area = (isset($_GET['area']) ? $_GET['area'] : 'A');
        $data = array(
            'pageTitle' => 'Menu 4',
            'label' => $this->dil->attributeLabels(),
            'sAjaxSource' => site_url('menu4/data?area=' . $area),
            'select' => array('IDPEL', 'NAMA', 'JENIS_MK', 'KDGARDU', 'NOTIANG'),
        );
        $data['sidebar']['dropdownData'] = array(
            'area' => $this->libarea->getList()
        );
        $this->layout->render('main', $data);
    }

    public function data() {
        if (empty($_GET['area'])) {
            return;
        }
        $select = array('IDPEL', 'NAMA', 'JENIS_MK', 'KDGARDU', 'NOTIANG');
        $filter = array(
            'select' => $select,
            'area' => (isset($_GET['area']) ? $_GET['area'] : 'A'),
            'limit' => (isset($_GET['iDisplayLength']) && $_GET['iDisplayLength'] != -1 ? intval($_GET['iDisplayLength']) : 50),
            'offset' => (isset($_GET['iDisplayStart']) ? intval($_GET['iDisplayStart']) : 0),
        );
        $sOrder = "";
        if (isset($_GET['iSortCol_0'])) {
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    $sOrder .= "`" . $select[intval($_GET['iSortCol_' . $i])] . "` " .
                            mysql_real_escape_string($_GET['sSortDir_' . $i]);
                }
                if ($i != 0 && $i + 1 == intval($_GET['iSortingCols']))
                    $sOrder .= ', ';
            }
        }

        $filter['order'] = $sOrder;
        $data = $this->libmenu4->getData($filter);
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
        );
        $output['aaData'] = $aaData;
        echo json_encode($output);
    }

    public function export() {
        $filter = array(
            'area' => (isset($_GET['area']) ? $_GET['area'] : 'A'),
            'controller' => $this->controller,
        );
        $this->libmenu4->export($filter);
    }

}

?>
