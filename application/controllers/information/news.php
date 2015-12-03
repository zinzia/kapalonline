<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OnlineBase.php' );

// --

class news extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_news');
        // exit
        redirect('home/welcome');
    }

    // view
    public function index($news_id = "") {
        // set template content
        $this->smarty->assign("template_content", "information/news/index.html");
        // data
        $result = $this->m_news->get_detail_news_published_by_id(array($news_id, $this->bahasa['lang_id']));
        if (empty($result)) {
            $result = $this->m_news->get_latest_news($this->bahasa['lang_id']);
        }
        // images
        $filepath = 'resource/doc/news/' . $result['news_id'] . '/' . $result['news_lang_img'];
        if (!is_file($filepath)) {
            $result['news_lang_img'] = '';
        } else {
            $result['news_lang_img'] = base_url() . $filepath;
        }
        $this->smarty->assign("result", $result);
        // news
        $rs_news = $this->m_news->get_list_news_published(array($this->bahasa['lang_id'], $result['news_id'], 0, 20));
        $this->smarty->assign("rs_news", $rs_news);
        // global variabel web content
        $this->m_lang->get_web_content_by_lang_group(array($this->bahasa['lang_id'], 'update_%'));
        // output
        parent::display();
    }

}
