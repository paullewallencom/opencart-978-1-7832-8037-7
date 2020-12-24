<?php  
class controllerModuleHelloworld extends Controller {
	protected function index() {
		$this->language->load('module/helloworld');
echo $this->affiliate->getCode();
      	$this->data['heading_title'] = $this->language->get('heading_title');
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->data['code'] = str_replace('http', 'https', html_entity_decode($this->config->get('helloworld_code')));
		} else {
			$this->data['code'] = html_entity_decode($this->config->get('helloworld_code'));
		}
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/helloworld.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/helloworld.tpl';
		} else {
			$this->template = 'default/template/module/helloworld.tpl';
		}
		
		$this->render();
	}
}
?>