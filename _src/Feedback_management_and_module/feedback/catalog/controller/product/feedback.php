<?php 
class ControllerProductfeedback extends Controller {  
	public function index() { 
		$this->language->load('product/feedback');	
		$this->load->model('catalog/feedback');		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else { 
			$page = 1;
		}	
		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = $this->config->get('config_catalog_limit');
		}				
		$this->data['breadcrumbs'] = array();
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
       		'separator' => false
   		);
		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_feedback'),
			'href'      => $this->url->link('product/feedback'),
       		'separator' => '::'
   		);	
	
	 	$this->document->setTitle($this->language->get('text_feedback'));
		$this->document->setDescription($this->language->get('text_description'));
		$this->document->setKeywords($this->language->get('text_keywords'));
		$this->data['heading_title'] = $this->language->get('text_feedback');
		$this->data['text_empty'] = $this->language->get('text_empty');
		$this->data['button_continue'] = $this->language->get('button_continue');

		$url = '';
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
					
		$this->data['feedbacks'] = array();	
		$data = array(
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
		);		
		$results = $this->model_catalog_feedback->getfeedbacks($data);
		$feedback_total = $this->model_catalog_feedback->getTotalFeedbacks(); 
		foreach ($results as $result) {
			$this->data['feedbacks'][] = array(
				'feedback_author'  => $result['feedback_author'],
				'description'  => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
				'href'  => $this->url->link('product/feedback', ''. $url)
			);
		}					
		$pagination = new Pagination();
		$pagination->total = $feedback_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('product/feedback','&page={page}');
		$this->data['pagination'] = $pagination->render();
		$this->data['limit'] = $limit;
		$this->data['continue'] = $this->url->link('common/home');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/feedback.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/product/feedback.tpl';
		} else {
				$this->template = 'default/template/product/feedback.tpl';
		}
			
		$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
		);
				
		$this->response->setOutput($this->render());										
  	}
}
?>