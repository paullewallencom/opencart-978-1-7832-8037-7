<?php
class ControllerCatalogFeedback extends Controller { 
	private $error = array();

	public function index() {
		$this->language->load('catalog/feedback');

		$this->document->setTitle($this->language->get('heading_feedback_author'));
		 
		$this->load->model('catalog/feedback');
		$this->load->model('setting/store');
		$this->getList();
	}

	public function insert() {
		$this->language->load('catalog/feedback');

		$this->document->setTitle($this->language->get('heading_feedback_author'));
		
		$this->load->model('catalog/feedback');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_feedback->addfeedback($this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->redirect($this->url->link('catalog/feedback', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function update() {
		$this->language->load('catalog/feedback');

		$this->document->setTitle($this->language->get('heading_feedback_author'));
		
		$this->load->model('catalog/feedback');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_feedback->editfeedback($this->request->get['feedback_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->redirect($this->url->link('catalog/feedback', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}
 
	public function delete() {
		$this->language->load('catalog/feedback');
		
		$this->load->model('catalog/feedback');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $feedback_id) {
				$this->model_catalog_feedback->deletefeedback($feedback_id);
			}
			
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->redirect($this->url->link('catalog/feedback', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'id.feedback_author';
		}
		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$url = '';
			
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_feedback_author'),
			'href'      => $this->url->link('catalog/feedback', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
							
		$this->data['insert'] = $this->url->link('catalog/feedback/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('catalog/feedback/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');	

		$this->data['feedbacks'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$feedback_total = $this->model_catalog_feedback->getTotalfeedbacks();
	
		$results = $this->model_catalog_feedback->getfeedbacks($data);
 
    	foreach ($results as $result) {
			$action = array();
						
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/feedback/update', 'token=' . $this->session->data['token'] . '&feedback_id=' . $result['feedback_id'] . $url, 'SSL')
			);
						
			$this->data['feedbacks'][] = array(
				'feedback_id' => $result['feedback_id'],
				'feedback_author'          => $result['feedback_author'],
				'sort_order'     => $result['sort_order'],
				'selected'       => isset($this->request->post['selected']) && in_array($result['feedback_id'], $this->request->post['selected']),
				'action'         => $action
			);
		}	
	
		$this->data['heading_feedback_author'] = $this->language->get('heading_feedback_author');

		$this->data['text_no_results'] = $this->language->get('text_no_results');

		$this->data['column_feedback_author'] = $this->language->get('column_feedback_author');
		$this->data['column_sort_order'] = $this->language->get('column_sort_order');
		$this->data['column_action'] = $this->language->get('column_action');		
		
		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_delete'] = $this->language->get('button_delete');
 
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$this->data['sort_feedback_author'] = $this->url->link('catalog/feedback', 'token=' . $this->session->data['token'] . '&sort=id.feedback_author' . $url, 'SSL');
		$this->data['sort_sort_order'] = $this->url->link('catalog/feedback', 'token=' . $this->session->data['token'] . '&sort=i.sort_order' . $url, 'SSL');
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $feedback_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('catalog/feedback', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->template = 'catalog/feedback_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	protected function getForm() {
		$this->data['heading_feedback_author'] = $this->language->get('heading_feedback_author');

		$this->data['text_default'] = $this->language->get('text_default');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
    	$this->data['text_disabled'] = $this->language->get('text_disabled');
		
		$this->data['entry_feedback_author'] = $this->language->get('entry_feedback_author');
		$this->data['entry_description'] = $this->language->get('entry_description');
		$this->data['entry_store'] = $this->language->get('entry_store');
		$this->data['entry_keyword'] = $this->language->get('entry_keyword');
		$this->data['entry_bottom'] = $this->language->get('entry_bottom');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
    	
		$this->data['tab_general'] = $this->language->get('tab_general');
    	$this->data['tab_data'] = $this->language->get('tab_data');
		$this->data['tab_design'] = $this->language->get('tab_design');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['feedback_author'])) {
			$this->data['error_feedback_author'] = $this->error['feedback_author'];
		} else {
			$this->data['error_feedback_author'] = array();
		}
		
		if (isset($this->error['description'])) {
			$this->data['error_description'] = $this->error['description'];
		} else {
			$this->data['error_description'] = array();
		}
		
		$url = '';
			
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),     		
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_feedback_author'),
			'href'      => $this->url->link('catalog/feedback', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
							
		if (!isset($this->request->get['feedback_id'])) {
			$this->data['action'] = $this->url->link('catalog/feedback/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('catalog/feedback/update', 'token=' . $this->session->data['token'] . '&feedback_id=' . $this->request->get['feedback_id'] . $url, 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('catalog/feedback', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['feedback_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$feedback_info = $this->model_catalog_feedback->getfeedback($this->request->get['feedback_id']);
		}
		
		$this->data['token'] = $this->session->data['token'];
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		if (isset($this->request->post['feedback_description'])) {
			$this->data['feedback_description'] = $this->request->post['feedback_description'];
		} elseif (isset($this->request->get['feedback_id'])) {
			$this->data['feedback_description'] = $this->model_catalog_feedback->getfeedbackDescriptions($this->request->get['feedback_id']);
		} else {
			$this->data['feedback_description'] = array();
		}

		$this->load->model('setting/store');
		
		$this->data['stores'] = $this->model_setting_store->getStores();
		
		if (isset($this->request->post['feedback_store'])) {
			$this->data['feedback_store'] = $this->request->post['feedback_store'];
		} elseif (isset($this->request->get['feedback_id'])) {
			$this->data['feedback_store'] = $this->model_catalog_feedback->getfeedbackStores($this->request->get['feedback_id']);
		} else {
			$this->data['feedback_store'] = array(0);
		}		
		
		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (!empty($feedback_info)) {
			$this->data['status'] = $feedback_info['status'];
		} else {
			$this->data['status'] = 1;
		}
				
		if (isset($this->request->post['sort_order'])) {
			$this->data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($feedback_info)) {
			$this->data['sort_order'] = $feedback_info['sort_order'];
		} else {
			$this->data['sort_order'] = '';
		}
		
		if (isset($this->request->post['feedback_layout'])) {
			$this->data['feedback_layout'] = $this->request->post['feedback_layout'];
		} elseif (isset($this->request->get['feedback_id'])) {
			$this->data['feedback_layout'] = $this->model_catalog_feedback->getfeedbackLayouts($this->request->get['feedback_id']);
		} else {
			$this->data['feedback_layout'] = array();
		}

		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
				
		$this->template = 'catalog/feedback_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/feedback')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['feedback_description'] as $language_id => $value) {
			if ((utf8_strlen($value['feedback_author']) < 3) || (utf8_strlen($value['feedback_author']) > 64)) {
				$this->error['feedback_author'][$language_id] = $this->language->get('error_feedback_author');
			}
		
			if (utf8_strlen($value['description']) < 3) {
				$this->error['description'][$language_id] = $this->language->get('error_description');
			}
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
			
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/feedback')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/store');
		
		foreach ($this->request->post['selected'] as $feedback_id) {
			if ($this->config->get('config_account_id') == $feedback_id) {
				$this->error['warning'] = $this->language->get('error_account');
			}
			
			if ($this->config->get('config_checkout_id') == $feedback_id) {
				$this->error['warning'] = $this->language->get('error_checkout');
			}
			
			if ($this->config->get('config_affiliate_id') == $feedback_id) {
				$this->error['warning'] = $this->language->get('error_affiliate');
			}
						
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>