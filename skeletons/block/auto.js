ccmValidateBlockForm = function() {
	if ($("#name").val() == '') { 
		ccm_addError(ccm_t('name-required'));
	}
	return false;
}
