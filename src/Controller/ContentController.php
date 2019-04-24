<?php
	namespace App\Controller;

	use App\Entity\Content;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Routing\Annotation\Route;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	
	class ContentController extends Controller
	{
		/**
		* @Route("/", name="dashboard")
		* @Method({"GET"})
		*/
	    public function index()
	    {
	    	/*
			* loading dashboard template
	    	*/
			return $this->render('contents/dashboard.html.twig', array('article' => array()));
	    }
		/**
		* @Route("/managecontent", name="content_list")
		* @Method({"GET"})
		*/
	    public function managecontent()
	    {
	    	/*
			* loading templates for all content available on the site
	    	*/
			return $this->render('contents/index.html.twig', array('article' => array()));
	    }

		/**
		* @Route("/contents/new", name="new_content")
		* @Method({"GET"})
		*/
	    public function newcontent()
	    {
	    	/*
			* loading templates for new content available on the site
	    	*/
	    	return $this->render('contents/addcontent.html.twig');
	    }
	}