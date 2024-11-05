<?php

use Symfony\Component\Config\Definition\Exception\Exception;

class MessagesController extends AppController{
    public $uses = array(
        'Chat',
        'Message',
        'User',
    );
    

    public function index() {
        try {
            if ($this->request->is('ajax')) {
                $this->autoRender = false;
            
                // The current user's ID
                $userId = $this->Auth->user('id');
            
                $offset = (int)$this->request->query('offset');
                $limit = (int)$this->request->query('limit');
                $searchTerm = $this->request->query('search'); // Get the search term
            
                // Prepare the base query
                $query = "
                SELECT 
                    chats.id, 
                    chats.sender_id, 
                    chats.recipient_id, 
                    last_messages.last_message, 
                    last_messages.last_created, 
                    users.name AS user_name,
                    users.imagepath AS user_imagepath
                FROM 
                    chats 
                LEFT JOIN 
                    (SELECT 
                        m.chat_id, 
                        m.content AS last_message, 
                        m.created AS last_created 
                    FROM 
                        messages m 
                    INNER JOIN 
                        (SELECT 
                            chat_id, 
                            MAX(created) AS max_created 
                        FROM 
                            messages 
                        WHERE
                            status != 0 
                        GROUP BY chat_id) AS latest 
                    ON 
                        m.chat_id = latest.chat_id AND m.created = latest.max_created) AS last_messages 
                ON 
                    last_messages.chat_id = chats.id
                LEFT JOIN 
                    users ON (users.id = chats.sender_id OR users.id = chats.recipient_id) AND users.id != ?
                WHERE 
                    (chats.sender_id = ? OR chats.recipient_id = ?)
                    AND chats.status != 0 
                    AND users.status != 0";
            
                // Add search condition if search term is provided
                if (!empty($searchTerm)) {
                    $query .= " AND users.name LIKE ?";
                }
            
                $query .= " 
                ORDER BY chats.modified DESC
                LIMIT $limit OFFSET $offset";
            
                // Prepare parameters
                $params = [
                    $userId, // user id check for exclusion
                    $userId, // sender_id in WHERE
                    $userId, // recipient_id in WHERE
                ];
            
                // Add search term to parameters if present
                if (!empty($searchTerm)) {
                    $params[] = '%' . $searchTerm . '%'; // Use wildcard for LIKE
                }
            
                // Execute the query
                $results = $this->Chat->query($query, $params);
            
                // Prepare the response
                $response = [
                    'success' => true,
                    'data' => $results
                ];
            
                // Set the response type and body
                $this->response->type('json');
                $this->response->body(json_encode($response));
                return $this->response;
            }
            
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
    
            $this->response->type('json');
            $this->response->body(json_encode($response));
            return $this->response;
        }
        $user = $this->User->findById($this->Auth->user('id'));
        $this->set('user', $user);
        $this->set('title_for_layout', 'Messages');
    }
    
    
    

    public function messageDetail($chatId = null) {
      if($this->request->is('ajax')){
            $this->autoRender = false;
            $offset = (int) $this->request->query('offset');
            $limit = (int) $this->request->query('limit');
            $searchTerm = $this->request->query('search');
        
            try {
                $conditions = [
                    'Message.status' => 1,
                    'Message.chat_id' => $chatId, // Condition for chat ID
                ];

                   if (!empty($searchTerm)) {
                    $conditions['Message.content LIKE'] = '%' . $searchTerm . '%';
                }
                
                // Perform the query with limit and offset
                $data = $this->Message->find('all', [
                    'conditions' => $conditions,
                    'joins' => [
                        [
                            'table' => 'users',
                            'alias' => 'Users',
                            'type' => 'INNER',
                            'conditions' => [
                                'Users.id = Message.sender_id'
                            ]
                        ]
                    ],
                    'fields' => ['Message.id', 'Message.content', 'Message.created', 'Message.sender_id', 'Users.imagepath'], // Select fields from messages and users
                    'limit' => $limit,  // Set the limit for pagination
                    'offset' => $offset, // Set the offset for pagination
                    'order' => ['Message.created' => 'DESC'] // Order by created date
                ]);
                
                // Format the result
                $result = [];
                foreach ($data as $message) {
                    $result[] = [
                        'id' => $message['Message']['id'],
                        'content' => $message['Message']['content'],
                        'created' => $message['Message']['created'],
                        'sender_id' => $message['Message']['sender_id'],
                        'user_imagepath' => !empty($message['Users']['imagepath']) ? $message['Users']['imagepath'] : null // Access the image path
                    ];
                }
                
                // Set the response type and body
                $this->response->type('json');
                $this->response->body(json_encode($result));
                return $this->response;
            } catch (Exception $e) {
                $this->response->status(500);
                $this->response->body(json_encode(['error' => 'Unable to load data']));
                return $this->response;
            }
        }

        $this->set('chatId', $chatId);
        $this->set('title_for_layout', 'Message details');
        $user = $this->User->findById($this->Auth->user('id'));
        $this->set('user', $user);
    }


    public function replyMessage(){
        if($this->request->is('ajax')){
            $this->autoRender = false;
            $content = $this->request->data('content');
            $chatId = $this->request->data('chatId');
            $this->request->data['Message']['sender_id']= AuthComponent::user('id');
            $this->request->data['Message']['status'] = 1;
            $this->request->data['Message']['is_read'] = 0;
            $this->request->data['Message']['chat_id'] = $chatId;
            $this->request->data['Message']['content'] = $content;
            if($this->Message->save($this->request->data)){
                // Update the chat's modified date
                $this->Chat->id = $chatId; // Set the chat ID for the record to update
                $this->Chat->set('modified', date('Y-m-d H:i:s')); // Set modified date
                    if($this->Chat->save()){
                        $response = ['success' => true];
                    }else{
                        $response = ['success' => false];
                    }
            }

            $this->response->type('json');
            $this->response->body(json_encode($response));
            return $this->response;
        }
    }

    public function add() {
        $this->set('title_for_layout', 'New Message');
    
        try {
            if ($this->request->is('ajax')) {
                $this->autoRender = false;
                $recipientId = $this->request->data('recipientId');
                $content = $this->request->data('content');
                $senderId = AuthComponent::user('id');
    
                // Check if a chat already exists between the sender and recipient
                $existingChat = $this->Chat->find('first', [
                    'conditions' => [
                        'OR' => [
                            'Chat.sender_id' => $senderId,
                            'Chat.recipient_id' => $senderId,
                        ],
                        'AND' => [
                            'OR' => [
                                'Chat.sender_id' => $recipientId,
                                'Chat.recipient_id' => $recipientId,
                            ]
                        ],
                        'Chat.status !=' => 0 
                    ]
                ]);
    
                if (!empty($existingChat)) {
                    // Chat exists; just add the message to the existing chat
                    $chatId = $existingChat['Chat']['id'];
    
                    $this->request->data['Message']['chat_id'] = $chatId;
                    $this->request->data['Message']['sender_id'] = $senderId;
                    $this->request->data['Message']['content'] = $content;
                    $this->request->data['Message']['is_read'] = 0;
                    $this->request->data['Message']['status'] = 1;
                    if ($this->Message->save($this->request->data)) {
                            // Update the chat's modified date
                        $this->Chat->id = $chatId; // Set the chat ID for the record to update
                        $this->Chat->set('modified', date('Y-m-d H:i:s')); // Set modified date
                        if($this->Chat->save()){
                            $response = ['success' => true];
                        }else{
                            $response = ['success' => false];
                        }
                    } else {
                        $response = ['success' => false];
                    }
                } else {
                    // No existing chat; create a new chat entry
                    $this->request->data['Chat']['sender_id'] = $senderId;
                    $this->request->data['Chat']['recipient_id'] = $recipientId;
                    $this->request->data['Chat']['status'] = 1;
    
                    if ($this->Chat->save($this->request->data)) {
                        // Get the last inserted ID
                        $lastInsertedId = $this->Chat->id;
    
                        // Prepare the message data
                        $this->request->data['Message']['chat_id'] = $lastInsertedId;
                        $this->request->data['Message']['sender_id'] = $senderId;
                        $this->request->data['Message']['content'] = $content;
                        $this->request->data['Message']['is_read'] = 0;
                        $this->request->data['Message']['status'] = 1;
    
                        if ($this->Message->save($this->request->data)) {
                            $response = ['success' => true];
                        } else {
                            $response = ['success' => false];
                        }
                    } else {
                        $response = ['success' => false];
                    }
                }
    
                $this->response->type('json');
                $this->response->body(json_encode($response));
                return $this->response;
            }
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'error' => $e->getMessage()
            ];
            $this->response->type('json');
            $this->response->body(json_encode($response));
            return $this->response;
        }
        $user = $this->User->findById($this->Auth->user('id'));
        $this->set('user', $user);
    }
    
    

    public function getRecipient(){
        if($this->request->is('ajax')){
            $this->autoRender = false;
            $users = $this->User->find('all', [
                'conditions' => ['User.status !=' => 0]
            ]);

            $this->response->type('json');
            $this->response->body(json_encode($users));
            return $this->response;
        }
    }

    public function deleteMessage(){
        $this->autoRender = false;
        try {
            if($this->request->is('ajax')){
                $id = $this->request->data('messageId');
                $this->request->data['Message']['status'] = 0;
                $this->Message->id = $id;
    
                if($this->Message->save($this->request->data)){
                    $response = [
                        'success' => true,
                        'message' => 'Message removed!'
                    ];
                }else{
                    $response = [
                        'success' => true,
                        'message' => 'Action failed'
                    ];
                }
                $this->response->type('json');
                $this->response->body(json_encode($response));
                return $this->response;
            }
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
            $this->response->type('json');
            $this->response->body(json_encode($response));
            return $this->response;
        }
    }

    public function deleteChat(){
        $this->autoRender = false;
        if($this->request->is('ajax')){
            $id = $this->request->data('id');
            $this->request->data['Chat']['status'] = 0;
            $this->Chat->id = $id;

            if($this->Chat->save($this->request->data)){
                $response = [
                    'success' => true,
                    'message' => 'Chat has been removed!'
                ];
            }else{
                $response = [
                    'success' => true,
                    'message' => 'Action failed'
                ];
            }
            $this->response->type('json');
            $this->response->body(json_encode($response));
            return $this->response;
        }
    }

}