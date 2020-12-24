<?php
class ModelTotalTips extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		
		if (isset($this->session->data['tips'])) {
		
				$total_data[] = array(
						'code'=> 'tips',
					'title' => 'Tips',
					'text' => $this->currency->format($this->session->data['tips']),
					'value' => $this->session->data['tips'],
					'sort_order' => '4'
      			);

				$total += $this->session->data['tips'];
			} 
		
	}
	
	public function confirm($order_info, $order_total) {
					
	}
}
?>