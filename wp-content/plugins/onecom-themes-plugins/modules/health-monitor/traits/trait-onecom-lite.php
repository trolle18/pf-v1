<?php

trait OnecomLite {

	function onecom_is_premium($callFor = NULL) {
        $features = oc_set_premi_flag();
        if($callFor === NULL) {
            if (isset($features['data']) && (!empty($features['data'])) && (in_array('MWP_ADDON', $features['data']))) {
                return true;
            }
            return false;
        } else if($callFor === 'all_plugins'){
            if (
                (isset($features['data']) && empty($features['data']))
                || (
                    in_array('ONE_CLICK_INSTALL', (array) $features['data'])
                    || in_array('MWP_ADDON', (array) $features['data'])
                )
            ) {
                return true;
            }
            return false;
        }
	}

	function onecom_premium_filter( $subtitle ) {
		if ( ! $this->onecom_is_premium() ) {
			return '';
		}

		return $subtitle;
	}
}
