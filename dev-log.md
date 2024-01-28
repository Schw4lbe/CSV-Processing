### prototype idea

- getting familiar with new challenge
- use way smaller csv
- reduce frontend functionality to handle backend challenges
- simpliefy single tasks to get used to it in bigger scales of csv data
- test final result in docker to provide result for externals

### new topics

- upload csv
- process csv
- create dynamic sql table
- set table values
- query table data in frontend
- update, delete db data

### initial steps to create prototype

- create frontend for csv file upload
- send csv to backend
- create sql table from csv data
- push csv data to created table
- query csv data and send to frontend
- display csv data in table
- make data changeable in frontend
- send update to backend
- update data in table
- also consider row deletion
- export data in frontend
- query data and store into new csv
- make csv downloadable in frontend

### features for frontend

- opening view with file selection
- file selection from local drive and upload button
- async upload api
- on success show data in second view (data view)
- display data table with queried data
- mark data row for editing
- update data in frontend
- on button click save send data to update in backend
- display updated result on frontend data table
- have export button
- on export button click prompt download directory
- save downloaded file to harddrive

### features for backend

- receive csv from frondend via api
- create new table in sql db
- create table header dynamic from incomming data
- define dynamic data types in table
- push csv data to created table
- update frontend after data import
- handle further data updates from fronend
- verify update data and sanatize data
- handle new data rows from frontend
- handle deletions from existing data rows from frontend
- provide csv export for download in frontend

### backlog

### versions

- successfully created stable version with initial file validation on front- and backend.
- therefore creating a new git tag to keep track on version 0.1
- command to create new tag: git tag -a v0.1.0-alpha -m "Early alpha release"
- command to push changes to git tags: git push origin --tags
