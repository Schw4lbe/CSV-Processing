### prototype idea

- getting familiar with new challenge
- use way smaller csv
- reduce frontend functionality to handle backend challenges
- simpliefy single tasks to get used to it in bigger scales of csv data
- test final result in docker to provide result for externals

### new topics

- delete and add item to db

### initial steps to create prototype

- delete item
- export data in frontend
- query data and store into new csv
- make csv downloadable in frontend

### features for frontend

- have export button
- on export button click prompt download directory
- save downloaded file to harddrive

### features for backend

- handle deletions from existing data from frontend
- provide csv export for download in frontend

### backlog

- create frontend animation while data is set in table
- update, export api
- therefor:

> Further Suggestions:
> API Documentation: Clearly document each API endpoint, detailing its purpose, input requirements, and output format.

> Error Handling: Implement robust error handling for each API to manage and respond to different error conditions gracefully.

> Security Measures: Apply appropriate authentication and authorization checks to ensure that only authorized users can access or modify data.

> Performance Optimization: Especially for the Export API, consider performance aspects like efficient data retrieval and handling large datasets.

##### next steps

- on next deploy new version 0.7 alpha

### versions

- command to create new tag: git tag -a v0.1.0-alpha -m "Early alpha release"
- command to push changes to git tags: git push origin --tags
