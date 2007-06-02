<?
	require_once('classes/objects/BorsBaseObject.php');

	class BorsImageGIF extends BorsBaseObject
	{
		function BorsPageGIF($id)
		{
			parent::BorsBasePage($id);
		}

		function preShowProcess()
		{
			$uri = $this->make_image();
			if(!$uri)
				return false;
			
			require_once('funcs/navigation/go.php');
			return go($uri, true, 0, false);
		}
	}
