# Aperture-Loans-Enterprise-System-Server
Public Repository containing the files used to operate a file system server for a Bank managing Loan information. Supports uploads, downloads and modifying operations as well as multiple searches based on multiple attributes of the files
such as loan number, date or file type. In addition, said functionalities have a robust logging systemn that logs any errors, uploads and searches, collecting error names, detailed descriptions, dates and session id's or in case of success
a record of the action, allowing system administrators to know exactly who, when and why an action or error ocurred. All of this logging information was stored in a mySQL database.

Furtheremore, The server makes us of ChronOS to hourly request updates on loans from the firms' file upload API as well as weekly audit the loan information in the database and download any loan files that might've been lost due to any connection errors or corruption

Made in an AWS Server using Ubuntu, nginx, PHP, mySQL, and Linux API's ChronOS chron functionality

functions.php contains the functionality to communicate to the api and request whatever neccesary

main.php contains the functionality to make request to the API every hour

upload_main, upload_new and upload_existing contain the hub to decide whether to upload new or existing files and their respective functionality

search_main.php and its derivatives contain the functionality to search based on certain parameter or list all files

unique_loans, c_loans, t_documents and t_ftype all contain functinoality to create reports based on information on the system.
