<?php

/**
 * estensione del converter Smarty per apidoc waLibs
 */
class HTMLSmartywaLibsConverter extends HTMLSmartyConverter
	{

	/** @var string */
	var $name = 'Smarty/waLibs';

	/**
	 * indexes of elements by package that need to be generated
	 * @var array
	 */
	var $leftindex = array('classes' => true, 'pages' => true, 'functions' => true, 'defines' => true, 'globals' => true);
	
	var $_defineleft_cache;

	// overloading: non mi interessa il contenuto delle pagine; mi interessa
	// solamente avere un link all'unica pagina che contiene delle defines
	// applicative
	function getDefineLeft()
		{
		if ($this->_defineleft_cache)
			{
			if (isset($this->_defineleft_cache[$this->package][$this->subpackage])) return $this->_defineleft_cache[$this->package][$this->subpackage];
			}
		$arr = array();
		if (isset($this->left[$this->package]))
			{
			foreach ($this->left[$this->package] as $subpackage => $pages)
				{
				// voglio mettere solo le pagine che hanno delle define
				foreach ($pages as $page)
					{
					foreach ($this->linkswithfile[$this->package][$this->subpackage]["define"] as $path => $nonmifrega)
						{
						if ($page["title"] == basename($path))
							{
							$page["title"] = "Defines applicative";
							$arr[$subpackage][] = $page;
							}
						}
					}
				}
			}

		$templ = &$this->newSmarty();
		$templ->assign('defineleftindex', $arr);
		$arr = $templ->fetch('defineleft.tpl');
		$this->_defineleft_cache[$this->package][$this->subpackage] = $arr;
		return $arr;
		}

	function getIndexInformation($elt)
		{
		$retval = parent::getIndexInformation($elt);
		$retval = str_replace("in file ", "in ", $retval);
		$retval = str_replace(" method ", " metodo ", $retval);
		$retval = str_replace(" variable ", " proprietà ", $retval);

		return $retval;
		}

	function formatLeftIndex()
		{
		phpDocumentor_out("\n");
		flush();
		$this->setTargetDir($this->base_dir);
		if (!isset($this->left))
			{
			debug("Nothing parsed, check the command-line");
			die();
			}
		foreach ($this->all_packages as $package => $rest)
			{
			if (!isset($this->pkg_elements[$package])) continue;
			// Create class tree page
			$template = &$this->newSmarty();
			$classleft = $this->getClassLeft();
			$template->assign("compiledfileindex", $this->getPageLeft());
			$template->assign("compileddefineindex", $this->getDefineLeft());
			$template->assign("compiledclassindex", $classleft['class']);
			$template->assign("compiledinterfaceindex", $classleft['interface']);
			$template->assign("classtrees", $this->generateFormattedClassTrees($package));
			$template->assign("interfaces", $this->generateFormattedInterfaceTrees($package));
			$template->assign("package", $package);
			$template->assign("date", date("r", time()));
			$template->assign("title", "Albero delle classi di $package");
			$template->register_outputfilter('HTMLSmarty_outputfilter');
			$this->writefile("classtrees_$package.html", $template->fetch('classtrees.tpl'));
			phpDocumentor_out("\n");
			flush();
			}
		$this->writeRIC();
		// free up considerable memory
		unset($this->elements);
		unset($this->pkg_elements);
		}

    /**
     * Writes out the template file of {@link $class_data} and unsets the template to save memory
     * @see registerCurrentClass()
     * @see parent::endClass()
     */
    function endClass()
    {
        $a = '../';
        if (!empty($this->subpackage)) $a .= '../';
        if ($this->juststarted)
        {
            $this->juststarted = false;
            phpDocumentor_out("\n");
            flush();
        }
        $this->setTargetDir($this->base_dir . PATH_DELIMITER . $this->class_dir);
        $classleft = $this->getClassLeft();
        $this->class_data->assign("compiledfileindex",$this->getPageLeft());
		$this->class_data->assign("compileddefineindex", $this->getDefineLeft());
        $this->class_data->assign("compiledclassindex",$classleft['class']);
        $this->class_data->assign("compiledinterfaceindex",$classleft['interface']);
        $this->class_data->assign("tutorials",$this->getTutorialList());
        $this->class_data->assign("contents",$this->getClassContents());
        $this->class_data->assign("packageindex",$this->package_index);
        $this->class_data->assign("package",$this->package);
        $this->class_data->assign("subdir",$a);
        $this->class_data->register_outputfilter('HTMLSmarty_outputfilter');
        $this->writefile($this->class . '.html',$this->class_data->fetch('class.tpl'));
        unset($this->class_data);
    }

    /**
     * Writes out the template file of {@link $page_data} and unsets the template to save memory
     * @see registerCurrent()
     * @see parent::endPage()
     */
    function endPage()
    {
        $this->package = $this->curpage->package;
        $this->subpackage = $this->curpage->subpackage;
        $a = '../';
        if (!empty($this->subpackage)) $a .= '../';
        $classleft = $this->getClassLeft();
        $this->setTargetDir($this->base_dir . PATH_DELIMITER . $this->page_dir);
        $this->page_data->assign("contents",$this->getPageContents());
        $this->page_data->assign("compiledfileindex",$this->getPageLeft());
		$this->page_data->assign("compileddefineindex", $this->getDefineLeft());
        $this->page_data->assign("compiledclassindex",$classleft['class']);
        $this->page_data->assign("compiledinterfaceindex",$classleft['interface']);
        $this->page_data->assign("tutorials",$this->getTutorialList());
        $this->page_data->assign("packageindex",$this->package_index);
        $this->page_data->assign("package",$this->package);
        $this->page_data->assign("subdir",$a);
        $this->page_data->register_outputfilter('HTMLSmarty_outputfilter');
        $this->writefile($this->page . '.html',$this->page_data->fetch('page.tpl'));
        unset($this->page_data);
    }

    /**
     * HTMLdefaultConverter chooses to format both package indexes and the complete index here
     *
     * This function formats output for the elementindex.html and pkgelementindex.html template files.  It then
     * writes them to the target directory
     * @see generateElementIndex(), generatePkgElementIndex()
     */
    function formatPkgIndex()
    {
        list($package_indexes,$packages,$mletters) = $this->generatePkgElementIndexes();
        for($i=0;$i<count($package_indexes);$i++)
        {
            $template = &$this->newSmarty();
            $this->package = $package_indexes[$i]['package'];
            $this->subpackage = '';
            $classleft = $this->getClassLeft();
            $template->assign("compiledfileindex",$this->getPageLeft());
			$template->assign("compileddefineindex", $this->getDefineLeft());
            $template->assign("compiledclassindex",$classleft['class']);
            $template->assign("compiledinterfaceindex",$classleft['interface']);
            $template->assign("tutorials",$this->getTutorialList());
            $template->assign("index",$package_indexes[$i]['pindex']);
            $template->assign("package",$package_indexes[$i]['package']);
            $template->assign("letters",$mletters[$package_indexes[$i]['package']]);
            $template->assign("title","Package ".$package_indexes[$i]['package']." Element Index");
            $template->assign("date",date("r",time()));
            $template->register_outputfilter('HTMLSmarty_outputfilter');
            $this->setTargetDir($this->base_dir);
            $this->writefile('elementindex_'.$package_indexes[$i]['package'].'.html',$template->fetch('pkgelementindex.tpl'));
        }
        phpDocumentor_out("\n");
        flush();
        }

    /**
     * HTMLdefaultConverter uses this function to format template index.html and packages.html
     *
     * This function generates the package list from {@link $all_packages}, eliminating any
     * packages that don't have any entries in their package index (no files at all, due to @ignore
     * or other factors).  Then it uses the default package name as the first package index to display.
     * It sets the right pane to be either a blank file with instructions on making package-level docs,
     * or the package-level docs for the default package.
     * @global string Used to set the starting package to display
     */
    function formatIndex()
    {
        global $phpDocumentor_DefaultPackageName;
        if (!isset($this->package_index))
        {
            debug("\nERROR: Nothing parsed, check the command-line");
            die();
        }
        list($elindex,$mletters) = $this->generateElementIndex();
        $template = &$this->newSmarty();
        $template->assign("index",$elindex);
        $template->assign("letters",$mletters);
        $template->assign("title","Element Index");
		$template->assign("package", false);
        $template->assign("date",date("r",time()));
        phpDocumentor_out("\n");
        flush();
        $this->setTargetDir($this->base_dir);
        $template->register_outputfilter('HTMLSmarty_outputfilter');
        $this->writefile('elementindex.html',$template->fetch('elementindex.tpl'));
        usort($this->package_index,"HTMLSmarty_pindexcmp");
        $index = &$this->newSmarty();
        foreach($this->all_packages as $key => $val)
        {
            if (isset($this->pkg_elements[$key]))
            {
                if (!isset($start)) $start = $key;
                if (!isset($this->package_pages[$key])) $this->writeNewPPage($key);
            }
        }
        // Created index.html
        $start = $phpDocumentor_DefaultPackageName;
        if (!isset($this->pkg_elements[$key]))
        {
            // if there are no elements, use a random package as the default
            $a = array_keys($this->pkg_elements);
            $start = array_shift($a);
        }
        $this->package = $start;
        $this->subpackage = '';
        $classleft = $this->getClassLeft();
        $index->assign("compiledfileindex",$this->getPageLeft());
		$index->assign("compileddefineindex", $this->getDefineLeft());
        $index->assign("compiledclassindex",$classleft['class']);
        $index->assign("compiledinterfaceindex",$classleft['interface']);
        $index->assign('hastodos',count($this->todoList));
        $index->assign('todolink','todolist.html');
        $index->assign("tutorials",$this->getTutorialList());
        $index->assign("date",date("r",time()));
        $index->assign("package",$this->package);
        $index->assign("title",$this->title);
        $index->assign("start","li_$start.html");
        if (isset($this->package_pages[$start]))
        {
            $index->assign("contents",$this->package_pages[$start]);
        }
        $index->register_outputfilter('HTMLSmarty_outputfilter');
        phpDocumentor_out("\n");
        flush();
        $this->setTargetDir($this->base_dir);
        $this->writefile("index.html",$index->fetch('index.tpl'));
        unset($index);

    }

    function writeNewPPage($key)
    {
        $template = &$this->newSmarty();
        $this->package = $key;
        $this->subpackage = '';
        $classleft = $this->getClassLeft();
        $template->assign("compiledfileindex",$this->getPageLeft());
		$template->assign("compileddefineindex", $this->getDefineLeft());
        $template->assign("compiledclassindex",$classleft['class']);
        $template->assign("compiledinterfaceindex",$classleft['interface']);
        $template->assign("tutorials",$this->getTutorialList());
        $template->assign("date",date("r",time()));
        $template->assign("title",$this->title);
        $template->assign("package",$key);
        $template->register_outputfilter('HTMLSmarty_outputfilter');
        phpDocumentor_out("\n");
        flush();
        $this->setTargetDir($this->base_dir);
        $this->writefile("li_$key.html",$template->fetch('index.tpl'));
        unset($template);
    }

    /**
     * Converts package page and sets its package as used in {@link $package_pages}
     * @param parserPackagePage
     */
    function convertPackagepage(&$element)
    {
        phpDocumentor_out("\n");
        flush();
        $template = &$this->newSmarty();
        $this->package = $element->package;
        $this->subpackage = '';
        $classleft = $this->getClassLeft();
        $template->assign("compiledfileindex",$this->getPageLeft());
		$template->assign("compileddefineindex", $this->getDefineLeft());
        $template->assign("compiledclassindex",$classleft['class']);
        $template->assign("compiledinterfaceindex",$classleft['interface']);
        $template->assign("tutorials",$this->getTutorialList());
        $template->assign("date",date("r",time()));
        $template->assign("title",$this->title);
        $template->assign("package",$element->package);
        $x = $element->Convert($this);
        $x = substr($x,strpos($x,'<body'));
        $template->assign("contents",trim(substr($x,strpos($x,'>') + 1)));
        $this->package_pages[$element->package] = trim(substr($x,strpos($x,'>') + 1));
        $template->register_outputfilter('HTMLSmarty_outputfilter');
        phpDocumentor_out("\n");
        flush();
        $this->setTargetDir($this->base_dir);
        $this->writefile("li_".$element->package.".html",$template->fetch('index.tpl'));
        unset($template);
    }

		
		
		
	}

