<?php

class Joobsbox_Form_Publish extends Joobsbox_Form_Base
{
    public function init()
    {
        $this->setAction($_SERVER['REQUEST_URI'])->setMethod('post')->setAttrib("id", "formPublish");
        $_model = $this->getModel('jobs');

		$title = $this->createElement('text', 'title')
			->setLabel('Job title:')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('notEmpty')
			->setDescription('Ex: "Flash Designer" or "ASP.NET Programmer"')
			->setRequired(true);
			
		$company = $this->createElement('text', 'company')
			->setLabel('Company:')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('notEmpty')
			->setRequired(true);
			
		$location = $this->createElement('text', 'location')
			->setLabel('Location:')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('notEmpty')
			->setDescription('Example: "London, Paris, Berlin, New York"')
			->setRequired(true);
			
		$categories[0] = $this->getView()->translate("Choose...");
		foreach($_model->fetchCategories()->getIndexNamePairs() as $key => $value) {
			$categories[$key] = $value;
		}
		
		$greaterThan = new Zend_Validate_GreaterThan(false, array('0'));
		$greaterThan->setMessage($this->getView()->translate("Choosing a category is mandatory."));
		$category = $this->createElement('select', 'category')
			->setLabel('Category:')
			->addValidator('notEmpty')
			->addValidator($greaterThan)
			->setRequired(true)
			->setMultiOptions($categories);
			
		$description = $this->createElement('textarea', 'description')
			->setLabel('Job description:')
			->setDescription('HTML code is not accepted. Length must be less than 4000 characters.')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('notEmpty')
			->setRequired(true);
		
		$application = $this->createElement('text', 'application')
			->setLabel('Means of application:')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('notEmpty')
			->setDescription('Ex: "Send CV to email ..." or "Apply online at URL ..."')
			->setRequired(true);
		
		$submit = $this->createElement('submit', 'submit')
			->setLabel("Add");
			
		$this->addElement($title)
			 ->addElement($company)
			 ->addElement($location)
			 ->addElement($category)
			 ->addElement($description)
			 ->addElement($application);
			 
		Joobsbox_Helpers_Filter::filterStatic('publish_form_init', $this);
		
		$publishNamespace = new Zend_Session_Namespace('PublishJob');
		if(isset($publishNamespace->editJobId)) {
			$jobData = $_model->fetchJobById($publishNamespace->editJobId);
			$title->setValue($jobData['title']);
			$company->setValue($jobData['company']);
			$location->setValue($jobData['location']);
			$category->setValue($jobData['categoryid']);
			$description->setValue($jobData['description']);
			$application->setValue($jobData['toapply']);
			
			Joobsbox_Helpers_Event::fire('job_form_edit', $this, $jobData);
			
			$exp = $this->createElement('text', 'expirationdate')
			  ->setLabel('Expiration date:')
			  ->addFilter('StripTags')
			  ->addFilter('StringTrim')
			  ->addValidator('notEmpty')
			  ->setRequired(true)
			  ->setValue(date("m/d/Y", $jobData['expirationdate']));
			$this->addElement($exp);
			
			$submit->setLabel('Modify');
		}
			
		$this->addElement($submit);
    }
}