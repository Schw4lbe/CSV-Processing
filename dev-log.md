### backlog

#### frontend

> new feature:

> FileUpload component

- remove alerts for wrong file format and no data and replace with modal
- make brows button bigger and change to light background and dark font
- on button click upload start animation for data table creation as modal
- same modal for error messages and loading animation
- after successfull upload display modal with success
- modal with fade out in center

> Chart Component:

- add close session button dialog with user prompt are you sure? yes/no
- update number values to be actuall percent information due to costumer request

> DataTable Component:

- after create new item success display modal success message
- after item edited success display modal success message
- after item deletion success display modal success message
- on button click csv export display modal and ask user are you sure?
- move export csv button next to header in top navbar

> all components:

- OPTIONAL: make light theme changeable from user
- refactor code
- write documentation

#### backend

- TBD
- refactor code
- write documentation

#### project total

- test final result in docker to provide result for externals

### sidenotes

> Further Suggestions:
> API Documentation: Clearly document each API endpoint, detailing its purpose, input requirements, and output format.

> Error Handling: Implement robust error handling for each API to manage and respond to different error conditions gracefully.

> Security Measures: Apply appropriate authentication and authorization checks to ensure that only authorized users can access or modify data.

> Performance Optimization: Especially for the Export API, consider performance aspects like efficient data retrieval and handling large datasets.

##### next version

- on next deploy new version 1.0.0 beta

### versioning

- command to create new tag: git tag -a v1.0.0-beta -m "message"
- command to push changes to git tags: git push origin --tags
