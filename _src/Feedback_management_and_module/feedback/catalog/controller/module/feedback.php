<?php  
class ControllerModuleFeedback extends Controller {
	protected function index($setting) {
		$this->language->load('module/feedback');
    	$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['viewall'] = $this->language->get('viewall');
		$this->load->model('catalog/feedback');
		$this->data['feedbacks'] = array();
		$this->data['href']=$this->url->link('product/feedback');
		$feedbacks = $this->model_catalog_feedback->getFeedbacks();
		foreach ($feedbacks as $feedback) {
			$this->data['feedbacks'][] = array(
				'feedback_id' => $feedback['feedback_id'],
				'feedback_author' => html_entity_decode($feedback['feedback_author'], ENT_QUOTES, 'UTF-8'),
				'description'    => html_entity_decode($feedback['description'], ENT_QUOTES, 'UTF-8')
			);	
		}
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/feedback.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/feedback.tpl';
		} else {
			$this->template = 'default/template/module/feedback.tpl';
		}
		$this->render();
  	}
}
?>