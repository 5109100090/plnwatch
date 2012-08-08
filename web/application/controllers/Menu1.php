<?php
/**
 * Description of Menu1
 *
 * @author spondbob
 */
class Menu1 extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->library('layout', array('controller' => 'menu1'));
        $this->load->library(array('LibMenu1','LibArea'));
        $this->load->helper(array('form'));
    }
    
    public function index(){
        $data = array();
        $data['dropdownData'] = array(
            'area' => $this->libarea->getList(),
            'meter' => $this->libmenu1->getListRangeMeter(),
            'tglPasang' => $this->libmenu1->getListRangeTglPasang()
        );
        $data['sidebar']['dropdownData'] = $data['dropdownData'];
        $data['pageTitle'] = 'Menu 1';
        $this->layout->render('main', $data);
    }
}

?>
