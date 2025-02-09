from openai import OpenAI

client = OpenAI(
 api_key = "FAKE", 
 base_url = "http://localhost:1337/v1"
)

response = client.chat.completions.create(
 model = "gpt-4o", 
 messages = [
   {
     "role" : "user",
     "content" : "Hello"
   }
  ],
)

print(response.choices[0].message.content)