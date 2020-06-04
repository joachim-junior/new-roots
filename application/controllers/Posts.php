<?php
class Posts extends CI_Controller
{
    public function index($offset = 0)
    {
        // Pagination Config
        $config['base_url'] = base_url() . 'posts/index/';
        $config['total_rows'] = $this->db->count_all('posts');
        $config['per_page'] = 20;
        $config['uri_segment'] = 20;
        $config['attributes'] = array('class' => 'pagination-link');

        // Init Pagination
        $this->pagination->initialize($config);

        $data['title'] = 'Latest Posts';

        $data['posts'] = $this->post_model->get_posts(false, $config['per_page'], $offset);

        $this->load->view('templates/header');
        $this->load->view('posts/index', $data);
        $this->load->view('templates/footer');
    }

    public function view($slug = null)
    {
        $data['post'] = $this->post_model->get_posts($slug);
        $post_id = $data['post']['pid'];
        $data['comments'] = $this->comment_model->get_comments($post_id);

        if (empty($data['post'])) {
            show_404();
        }

        $data['title'] = $data['post']['title'];

        $this->load->view('templates/header');
        $this->load->view('posts/view', $data);
        $this->load->view('templates/footer');
    }

    public function create()
    {
        // Check login
        if (!$this->session->userdata('logged_in')) {
            redirect('users/login');
        }

        $data['title'] = 'Create Post';

        $data['countries'] = $this->post_model->get_countries();

        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('body', 'Body', 'required');

        if ($this->form_validation->run() === false) {
            $this->load->view('templates/header');
            $this->load->view('posts/create', $data);
            $this->load->view('templates/footer');
        } else {
            // Upload Image
            $config['upload_path'] = './assets/images/posts';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|mp4';
            $config['max_size'] = '2048';
            $config['max_width'] = '2000';
            $config['max_height'] = '2000';

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload()) {
                $errors = array('error' => $this->upload->display_errors());
                $this->session->set_flashdata('upload_error', 'Failed to upload. This may be an internet error or your file size and/or dimensions');
                redirect('posts/create');
            } else {

                $data = array('upload_data' => $this->upload->data());
                $string = $_FILES['userfile']['name'];
                $trimmed = trim($string);
                $post_image = str_replace(' ', '_', $trimmed);

                $config['image_library'] = 'gd2';
                $config['source_image'] = '../images/post/' . $post_image;
                $config['create_thumb'] = true;
                $config['maintain_ratio'] = true;
                $config['width'] = 300;
                $config['height'] = 250;

                $this->load->library('image_lib', $config);

                $this->image_lib->resize();
            }

            $this->post_model->create_post($post_image);

            // Set message
            $this->session->set_flashdata('post_created', 'Your post has been created');

            redirect('posts');
        }
    }

    public function delete($id)
    {
        // Check login
        if (!$this->session->userdata('logged_in')) {
            redirect('users/login');
        }

        $this->post_model->delete_post($id);

        // Set message
        $this->session->set_flashdata('post_deleted', 'Your post has been deleted');

        redirect('posts');
    }

    public function edit($slug)
    {
        // Check login
        if (!$this->session->userdata('logged_in')) {
            redirect('users/login');
        }

        $data['post'] = $this->post_model->get_posts($slug);

        // Check user
        if ($this->session->userdata('user_id') != $this->post_model->get_posts($slug)['id']) {
            redirect('posts');
        }

        $data['countries'] = $this->post_model->get_countries();

        if (empty($data['post'])) {
            show_404();
        }

        $data['title'] = 'Edit Post';

        $this->load->view('templates/header');
        $this->load->view('posts/edit', $data);
        $this->load->view('templates/footer');
    }

    public function update()
    {
        // Check login
        if (!$this->session->userdata('logged_in')) {
            redirect('users/login');
        }

        $this->post_model->update_post();

        // Set message
        $this->session->set_flashdata('post_updated', 'Your post has been updated');

        redirect('posts');
    }
}
