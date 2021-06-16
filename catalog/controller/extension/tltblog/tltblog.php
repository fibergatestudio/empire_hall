<?php
class ControllerExtensionTltBlogTltBlog extends Controller {
	public function index() {
		$this->load->language('extension/tltblog/tltblog');

		$this->load->model('extension/tltblog/tltblog');
		$this->load->model('catalog/product');
		$this->load->model('setting/setting');
		$this->load->model('tool/image');

		if ($this->config->get('tltblog_seo')) {
			require_once(DIR_APPLICATION . 'controller/extension/tltblog/tltblog_seo.php');
			$tltblog_seo = new ControllerExtensionTltBlogTltBlogSeo($this->registry);
			$this->url->addRewrite($tltblog_seo);
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

        if ($this->config->has('tltblog_path')) {
            $path_array = $this->config->get('tltblog_path');
        }

        if (isset($this->request->get['tltpath'])) {
            $path = $this->request->get['tltpath'];
        } elseif (isset($path_array[$this->config->get('config_language_id')])) {
            $path = $path_array[$this->config->get('config_language_id')];
        } else {
            $path = 'blogs';
        }
		
		$data['show_path'] = $this->config->get('tltblog_show_path');

		if (isset($this->request->get['tltblog_id'])) {
			$tltblog_id = (int)$this->request->get['tltblog_id'];
		} else {
			$tltblog_id = 0;
		}

		$tltblog_info = $this->model_extension_tltblog_tltblog->getTltBlog($tltblog_id);

		if ($tltblog_info) {
			$this->document->setTitle($tltblog_info['meta_title']);
			$this->document->setDescription($tltblog_info['meta_description']);
			$this->document->setKeywords($tltblog_info['meta_keyword']);

			$this->document->addLink($this->url->link('extension/tltblog/tltblog', 'tltpath=' . $path . '&tltblog_id=' . $tltblog_id), 'canonical');

            $data['breadcrumbs'][] = array(
                'text' => $tltblog_info['title'],
                'href' => $this->url->link('extension/tltblog/tltblog', 'tltblog_id=' .  $tltblog_id)
            );

			$data['heading_title'] = $tltblog_info['title'];
			$data['show_title'] = $tltblog_info['show_title'];

			if ($tltblog_info['image']) {
				if ($this->request->server['HTTPS']) {
					$data['blog_image'] = $this->config->get('config_ssl') . 'image/' . $tltblog_info['image'];
				} else {
					$data['blog_image'] = $this->config->get('config_url') . 'image/' . $tltblog_info['image'];
				}
			} else {
				$data['blog_image'] = '';
			}

			$data['date'] = date($this->language->get('date_format_short'), strtotime($tltblog_info['date_available']));
			$data['description'] = html_entity_decode($tltblog_info['description'], ENT_QUOTES, 'UTF-8');
			$data['intro'] = strip_tags(html_entity_decode($tltblog_info['intro'], ENT_NOQUOTES, 'UTF-8'));
			$data['meta_description'] = $tltblog_info['meta_description'];
            $data['all_news'] = $this->url->link('extension/tltblog/tlttag', 'tlttag_id=1');

			$data['slides'] = array();
			$gallery = $this->model_extension_tltblog_tltblog->getGallery($tltblog_id);

			foreach($gallery as $slide) {
				
				if($slide['image'] && is_file(DIR_IMAGE . $slide['image'])){
					$image_now = $this->model_tool_image->resize($slide['image'], 1200, 600);
				} else {
					$image_now = false;
				}
				
				$data['slides'][] = array(
					'image' => $image_now,
					'title' => $slide['title'][$this->config->get('config_language_id')]
				);
			}

			// Get similar articles
            $similar_articles = $this->model_extension_tltblog_tltblog->getTltBlogs(3);

			if ($similar_articles) {
                foreach ($similar_articles as $tltblog) {
                    if ($tltblog['image']) {
                        $image = $this->model_tool_image->resize($tltblog['image'], 450, 320);
                    } else {
                        $image = $this->model_tool_image->resize('placeholder.png', 450, 320);
                    }

                    $data['articles'][] = array(
                        'id'    => $tltblog['tltblog_id'],
                        'image' => $image,
                        'title' => $tltblog['title'],
                        'date'  => date($this->language->get('date_format_short'), strtotime($tltblog['date_available'])),
                        'href'  => $this->url->link('extension/tltblog/tltblog', 'tltblog_id=' . $tltblog['tltblog_id'])
                    );
                }
            }

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('extension/tltblog/tltblog', $data));
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('extension/tltblog/tltblog', 'tltpath=' . $path . '&tltblog_id=' . $tltblog_id)
			);

			$this->document->setTitle($this->language->get('text_error'));
            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

            $data['text_error'] = $this->language->get('text_error');
			$data['heading_title'] = $this->language->get('text_error');
			$data['button_continue'] = $this->language->get('button_continue');
			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}
}