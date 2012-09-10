<?php

/**
 * Description of Menu3
 *
 * @author spondbob
 */
class Menu3Controller extends CI_Controller {

    private $controller = 'Menu3';
    private $activeUser = null;

    public function __construct() {
        parent::__construct();
        $this->load->library('layout', array('controller' => strtolower($this->controller)));
        $this->load->library(array('LibMenu3', 'LibArea'));
        $this->load->helper(array('form'));
        $this->load->model('sorek');
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
        $lib = new LibMenu3();
        $input = array(
            'kodearea' => $this->input->get('area'),
            'tren' => $this->input->get('tren'),
        );
        $input = $lib->validateInput($input);
        
        $label = $this->sorek->getTrenLabels();
        array_unshift($label, 'ID Pelanggan');
        $data = array(
            'pageTitle' => 'Menu 3',
            'label' => $label,
            'sAjaxSource' => site_url('menu3/data?area='.$input['kodearea'].'&tren='.$input['tren']),
        );
        $data['dropdownData'] = array(
            'area' => $this->libarea->getList(),
            'tren' => $this->libmenu3->getListTren()
        );
        $data['sidebar']['dropdownData'] = $data['dropdownData'];
        $this->layout->render('main', $data);
    }
    
    public function data() {
        $lib = new LibMenu3();
        $filter = array(
            'kodearea' => $this->input->get('area'),
            'tren' => $this->input->get('tren'),
        );
        $filter = $lib->validateInput($filter);
        $filter['limit'] = (isset($_GET['iDisplayLength']) && $_GET['iDisplayLength'] != -1 ? intval($_GET['iDisplayLength']) : 25);
        $filter['offset'] = (isset($_GET['iDisplayStart']) ? intval($_GET['iDisplayStart']) : 0);
        
        $data = $lib->getData($filter);
        $aaData = array();
        foreach ($data['data'] as $d) {
            $aaData[] = array_values($d);
        }
        $output = array(
            "sEcho" => (isset($_GET['sEcho']) ? intval($_GET['sEcho']) : 1),
            "iTotalRecords" => $data['num'],
            "iTotalDisplayRecords" => $data['num'],
        );
        $output['aaData'] = $aaData;
        echo json_encode($output);
    }
    
    public function tes() {
        $this->sorek->getTrenTurun();
    }

}

?>
