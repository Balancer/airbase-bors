<?php

/*
	Используется https://github.com/Gregwar/Tex2png
	composer require gregwar/tex2png=*
	sudo apt-get install texlive-pstricks
	sudo apt-get install texlive-science
	sudo apt-get install texlive-bibtex-extra
	sudo apt-get install texlive-latex-extra
*/

class airbase_cg_chem extends bors_object
{
	function content()
	{
		$chem = $this->id();

/*
$chem = "\documentclass{article}
\usepackage{xymtex}
\usepackage{chemist}
\pagestyle{empty}
\begin{document}
{$chem}
\end{document}";
*/
		$wrapper = Gregwar\Tex2png\Tex2png::create($chem, 100)
			->saveTo($this->attr('save_to'));
		$wrapper->packages = ['xymtex', 'chemist']; // array('amssymb,amsmath', 'color', 'amsfonts', 'amssymb', 'pst-plot');
		$wrapper->generate();

		if($wrapper->error)
			bors_throw($wrapper->error);

//		r($i, file_exists('/tmp/sum.png'));

		return file_get_contents($this->attr('save_to'));
	}
}
