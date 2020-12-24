<?php
class ModelCatalogFeedback extends Model {
public function getFeedbacks() {
$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "feedback fc   LEFT JOIN " . DB_PREFIX . "feedback_description fcd ON (fc.feedback_id = fcd.feedback_id) LEFT JOIN " . DB_PREFIX . "feedback_to_store fc2s ON (fc.feedback_id = fc2s.feedback_id) WHERE fcd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND fc2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND fc.status = '1'");
	
return $query->rows;
}
						
public function getTotalFeedbacks() {
$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "feedback fc LEFT JOIN " . DB_PREFIX . "feedback_to_store fc2s ON (fc.feedback_id = fc2s.feedback_id) WHERE fc2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND fc.status = '1'");
	
return $query->row['total'];
}}
?>
>