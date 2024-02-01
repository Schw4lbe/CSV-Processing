### prototype idea

- getting familiar with new challenge
- use way smaller csv
- reduce frontend functionality to handle backend challenges
- simpliefy single tasks to get used to it in bigger scales of csv data
- test final result in docker to provide result for externals

### new topics

- update, delete db data

### initial steps to create prototype

- make data changeable in frontend
- send update to backend
- update data in table
- also consider row deletion
- export data in frontend
- query data and store into new csv
- make csv downloadable in frontend

### features for frontend

- update data in frontend
- on button click save send data to update in backend
- display updated result on frontend data table
- have export button
- on export button click prompt download directory
- save downloaded file to harddrive

### features for backend

- handle further data updates from fronend
- verify update data and sanatize data
- handle new data rows from frontend
- handle deletions from existing data rows from frontend
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

- refactor backend
- deploy new version 0.3 alpha

### versions

- command to create new tag: git tag -a v0.1.0-alpha -m "Early alpha release"
- command to push changes to git tags: git push origin --tags
