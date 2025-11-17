<?php 

class projects_model extends CI_Model{
    public function __construct()
        {
            $this->load->database();
        }

    public function get_projects(){
        $query = $this->db->get('projects');
        
        return $query->result();
}

public function get_project($id){
	return $this->db->get_where('projects', ['id' => $id])->row();
}

 public function add_project($name, $description, $created_at) {
        $data = [
            'name' => $name,
            'description' => $description,
			'created_at' => $created_at
        ];

        $this->db->insert('projects', $data);
		return $this->db->insert_id();
    }

    public function delete_project($id) {
        $this->db->delete('projects',array('id'=>$id));

    }

}

