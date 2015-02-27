<?php

/*
	Используется https://github.com/Gregwar/Tex2png
	composer require gregwar/tex2png=*
	sudo apt-get install texlive-pstricks
	sudo apt-get install texlive-science		— пакеты mhchem // http://www.johndcook.com/blog/tag/latex/ или http://tex.stackexchange.com/questions/14010/chemistry-equations
	sudo apt-get install texlive-bibtex-extra
	sudo apt-get install texlive-latex-extra
*/

class airbase_cg_chem extends bors_object
{
	function content_type()
	{
		return 'image/png';
	}

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
		$wrapper = Gregwar\Tex2png\Tex2png::create($chem, $this->attr('size', 100))
			->saveTo($this->attr('save_to'));
		$wrapper->packages = ['xymtex', 'chemist', 'mhchem', 'amssymb']; // array('amssymb,amsmath', 'color', 'amsfonts', 'amssymb', 'pst-plot');
		$wrapper->generate();

		if($wrapper->error)
			bors_throw($wrapper->error);

//		r($i, file_exists('/tmp/sum.png'));

		return file_get_contents($this->attr('save_to'));
	}
}
