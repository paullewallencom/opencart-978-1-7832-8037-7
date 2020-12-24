<?php
class ModelCatalogfeedback extends Model {
	public function addfeedback($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "feedback SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "'");

		$feedback_id = $this->db->getLastId(); 
		
		foreach ($data['feedback_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "feedback_description SET feedback_id = '" . (int)$feedback_id . "', language_id = '" . (int)$language_id . "',  feedback_author= '" . $this->db->escape($value['feedback_author']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}
		
		if (isset($data['feedback_store'])) {
			foreach ($data['feedback_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "feedback_to_store SET feedback_id = '" . (int)$feedback_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['feedback_layout'])) {
			foreach ($data['feedback_layout'] as $store_id => $layout) {
				if ($layout) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "feedback_to_layout SET feedback_id = '" . (int)$feedback_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout['layout_id'] . "'");
				}
			}
		}
		$this->cache->delete('feedback');
	}
	
	public function editfeedback($feedback_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "feedback SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "' WHERE feedback_id = '" . (int)$feedback_id . "'");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "feedback_description WHERE feedback_id = '" . (int)$feedback_id . "'");
					
		foreach ($data['feedback_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "feedback_description SET feedback_id = '" . (int)$feedback_id . "', language_id = '" . (int)$language_id . "',  feedback_author= '" . $this->db->escape($value['feedback_author']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "feedback_to_store WHERE feedback_id = '" . (int)$feedback_id . "'");
		
		if (isset($data['feedback_store'])) {
			foreach ($data['feedback_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "feedback_to_store SET feedback_id = '" . (int)$feedback_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "feedback_to_layout WHERE feedback_id = '" . (int)$feedback_id . "'");

		if (isset($data['feedback_layout'])) {
			foreach ($data['feedback_layout'] as $store_id => $layout) {
				if ($layout['layout_id']) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "feedback_to_layout SET feedback_id = '" . (int)$feedback_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout['layout_id'] . "'");
				}
			}
		}
				
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'feedback_id=" . (int)$feedback_id. "'");
		$this->cache->delete('feedback');
	}
	
	public function deletefeedback($feedback_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "feedback WHERE feedback_id = '" . (int)$feedback_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "feedback_description WHERE feedback_id = '" . (int)$feedback_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "feedback_to_store WHERE feedback_id = '" . (int)$feedback_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "feedback_to_layout WHERE feedback_id = '" . (int)$feedback_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'feedback_id=" . (int)$feedback_id . "'");

		$this->cache->delete('feedback');
	}	

	public function getfeedback($feedback_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'feedback_id=" . (int)$feedback_id . "') AS keyword FROM " . DB_PREFIX . "feedback WHERE feedback_id = '" . (int)$feedback_id . "'");
		
		return $query->row;
	}
		
	public function getfeedbacks($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "feedback i LEFT JOIN " . DB_PREFIX . "feedback_description id ON (i.feedback_id = id.feedback_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
			$sort_data = array(
				'id.feedback_author',
				'i.sort_order'
			);		
		
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY id.feedback_author";	
			}
			
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}
		
			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}		

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}	
			
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}	
			
			$query = $this->db->query($sql);
			
			return $query->rows;
		} else {
			$feedback_data = $this->cache->get('feedback.' . (int)$this->config->get('config_language_id'));
		
			if (!$feedback_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "feedback i LEFT JOIN " . DB_PREFIX . "feedback_description id ON (i.feedback_id = id.feedback_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY id.");
	
				$feedback_data = $query->rows;
			
				$this->cache->set('feedback.' . (int)$this->config->get('config_language_id'), $feedback_data);
			}	
	
			return $feedback_data;			
		}
	}
	
	public function getfeedbackDescriptions($feedback_id) {
		$feedback_description_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "feedback_description WHERE feedback_id = '" . (int)$feedback_id . "'");

		foreach ($query->rows as $result) {
			$feedback_description_data[$result['language_id']] = array(
				'feedback_author' => $result['feedback_author'],
				'description' => $result['description']
			);
		}
		
		return $feedback_description_data;
	}
	
	public function getfeedbackStores($feedback_id) {
		$feedback_store_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "feedback_to_store WHERE feedback_id = '" . (int)$feedback_id . "'");

		foreach ($query->rows as $result) {
			$feedback_store_data[] = $result['store_id'];
		}
		
		return $feedback_store_data;
	}

	public function getfeedbackLayouts($feedback_id) {
		$feedback_layout_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "feedback_to_layout WHERE feedback_id = '" . (int)$feedback_id . "'");
		
		foreach ($query->rows as $result) {
			$feedback_layout_data[$result['store_id']] = $result['layout_id'];
		}
		
		return $feedback_layout_data;
	}
		
	public function getTotalfeedbacks() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "feedback");
		
		return $query->row['total'];
	}	
	
	public function getTotalfeedbacksByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "feedback_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}	
}
?>