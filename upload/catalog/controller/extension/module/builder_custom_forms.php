<?php
class ControllerExtensionModuleBuilderCustomForms extends Controller {
    public function index($setting) {
        $data['html'] = html_entity_decode($setting['renderForm']);
        $data['token'] = token(4);
        $data['call_to_action'] = $setting['call_to_action'];
        $data['template'] = $setting['template'];
        $data['email'] = $setting['email'];
        $data['css_custom'] = $setting['css_custom'];
        $data['custom_action'] = $setting['custom_action'];
        return $this->load->view('extension/module/builder_custom_forms', $data);
    }

    public function submit(){

        $json = array();

        $this->log->write($this->request->post);

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {

            if (!isset($this->request->post['email_to'])) {
                $json['error']['warning'] = $this->language->get('error_missing_email_to');
            }

            if (!isset($json['error'])){

                $mail = new Mail($this->config->get('config_mail_engine'));
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

                $mail->setTo($this->request->post['email_to']);
                $mail->setFrom($this->request->post['email_to']);
                $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
                $mail->setSubject(html_entity_decode($this->language->get('text_new_affiliate'), ENT_QUOTES, 'UTF-8'));
                if (isset($this->request->post['template_name'])){
                    $mail->setText($this->load->view($this->request->post['template_name'], $this->request->post));
                } else {
                    $mail->setText($this->request->post);
                }
                $mail->send();

                $json['success'] = 'Inviato con successo!';
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}