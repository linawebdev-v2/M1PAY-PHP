# Introduction

To support M1Pay Payment Integration in PHP language for MobilityOne.
The development is using Docker.

# Requirements
1- Register to M1Pay first  

# Process to Setup M1Pay Payment Integration
1- Initial setup usualy on M1Pay UAT Environment before live in production server.
2- After registration and M1 approve, merchant may obtain the credential such as  
   - Client ID  
   - Client Secret  
   - File of private key  
   - File of public key  

3- Merchant's developer need to copy and paste `.env_sample` and rename the file as `.env`  
4- The Client ID and Client Secret value must place in .env accordingly.  
5- The key files must placed in merchant's server and set the path of the files under 'PAYMENT_CERTS' accordingly.  

If you have inquiry related to the codes, email to lina.webdev@gmail.com 
