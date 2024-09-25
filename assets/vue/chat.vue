<template>
  
    <div class="h-[30vh] w-[100vh] overflow-y-scroll">
      <div v-for="(msg, index) in chat" :key="index">
        <div v-if="msg.type === 'user'" class="w-fit bg-semi-transparent-1 pl-5 pr-5 pt-0 pb-0 flex items-end justify-end ml-auto mt-2 mb-2 rounded">{{ msg.content }}</div>
        <div v-else class="bg-semi-transparent-2 pl-5 pr-5 pt-0 pb-0 mt-2 mb-2 rounded max-w-[90vh]">{{ msg.content }}</div>
      </div>
    </div>
    <div class="row w-full h-fit centered">
      <textarea class="column relative w-full h-[10vh] border-[1px] border-semi-transparent-3 p-5 rounded-lg resize-none shadow-lg bg-semi-transparent-1 shadow-shadow" @keyup.enter="sendMessage" v-model="userMessage" required="required" placeholder="." maxlength="800" ></textarea>
      <button @click="sendMessage" class="h-full aButton ml-4">Enter</button>
    </div>
    
</template>

<script>
export default {
  data() {
    return {
      userMessage: '', // User input message
      chat: [],        // Array to store chat messages
    };
  },
  methods: {
    async sendMessage() {
      if (!this.userMessage.trim()) return; // Avoid sending empty messages
      console.log('preparing...')
      // Add user message to chat
      this.chat.push({ type: 'user', content: this.userMessage });

      // Prepare request to Symfony backend
      try {
          const response = await fetch('/chatbot', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(this.userMessage),
          })
              .then(response => {
                if (response.status === 200) {
                  return response.json();
                }
                else{
                  throw new Error(`!response.ok - HTTP error! status: ${response.status}`);
                }
              })
              .then(data => {
                console.log(data);
                // Add bot response to chat
                this.chat.push({ type: 'bot', content: data.response });
                // Clear the user input
                this.userMessage = '';
              })
        }
        catch (error) {
          console.error('Error fetching products:', error);
        }
    },
  },
};
</script>

<style>
#chatbox {
  width: 400px;
  border: 1px solid #ccc;
  padding: 10px;
}

#messages {
  height: 200px;
  overflow-y: scroll;
  margin-bottom: 10px;
}

.user-message {
  text-align: right;
  background-color: #e0e0e0;
  padding: 5px;
  margin: 5px 0;
}

.bot-response {
  text-align: left;
  background-color: #f0f0f0;
  padding: 5px;
  margin: 5px 0;
}

input {
  width: calc(100% - 80px);
}

button {
  width: 60px;
}
</style>
