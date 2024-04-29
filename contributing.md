## Contribution guidelines to RESIDAT Project,
You may contribute to RESIDAT in the following ways;
•	As a Developer
•	As a manual tester
•	As an automation tester
•	As a translator

# As a Developer
Required knowledge

To contribute to the RESIDAT project as a developer, you will be required to have basic knowledge and understanding of Javascript, PHP, python coding languages and these tool/s Laravel and VueJS. We also strongly recommend you be familiar with Adobe that might not be as important as the other two mentioned but is still important.
As a translator
We currently support English and French, but we are open to adding new languages as users' needs arise. We are however primarily focused in piloting and growing in Francophone and Anglophone sub Saharan African countries. 
		Setting up your IDE

# Reporting an issue
Found a bug in RESIDAT? Here are some notes on how to report the bug so we can fix it as fast as possible:
•	Explain, as detailed as possible, how to reproduce the issue.
•	Include what you expected to happen, as well as what actually happened.
•	If it's a bug with the website, please include information on what browser version and operating system you are running.
•	If it helps, feel free to attach a screenshot or video illustrating the issue.
•	If you're having trouble with a specific build, please include a link to the build or job in question.
•	Include all this information in a new issue on our Issue Tracker

# Submitting a pull request
Know how to fix something? We love pull requests! Here's a quick guide:
1.	Check for open issues, or open a fresh issue to start a discussion around a feature idea or a bug. Opening a separate issue to discuss the change is less important for smaller changes, as the discussion can be done in the pull request.
2.	Fork the relevant repository on GitHub, and start making your changes.
3.	Check out the README for the project for information specific to that repository.
4.	Push the change (we recommend using a separate branch for your feature).
5.	Open a pull request.
6.	We try to merge and deploy changes as soon as possible, or at least leave some feedback, but if you haven't heard back from us after a couple of days, feel free to leave a comment on the pull request.
Commits
We use the Conventional Commits specification for our commit messages. Under this convention the commit message should be structured like this:
<type>[optional scope]: <description>

[optional body]

[optional footer(s)]

# Bear in mind:
1) Type fix:: patches a bug in your codebase. 
2) Type feat:: introduces a new feature to the codebase (this correlates with MINOR in Semantic Versioning). 
3) Types other than fix:: and feat:: are allowed, for example build:, chore:, ci:, docs:, style:, refactor:, perf:, test:. 4) Footer BREAKING CHANGE or ! after type/scope: introduces a breaking API change (correlating with MAJOR in Semantic Versioning). 5) A BREAKING CHANGE can be part of commits of any type. 6) Footers other than BREAKING CHANGE may be provided and follow a convention similar to the git trailer format.

[ Examples ]

Commit message with description and BREAKING CHANGE footer:
feat: allow provided config object to extend other configs BREAKING CHANGE: extends key in config file is now used for extending other config files
Commit message with scope and ! to draw attention to breaking change
*feat(api)!: send an email to the customer when a product is shipped
Commit message with both ! and BREAKING CHANGE footer
chore!: drop support for Node 6 BREAKING CHANGE: use JavaScript features not available in Node 6.
Branch naming
To name and describe our branches we use the type of change it will contain and a short description, following Git branching models.
Examples:
Instance	Branch	Description, Instructions, Notes
Stable	main	Accepts merges from Working and Hotfixes
Development	dev	Accepts merges from Features/Issues, Fixes and Hotfixes
Features/Issues	feat/*	Always branch off HEAD of Working
Fixes	fix/*	Always branch off HEAD of Working
Hotfix	hotfix/*	Always branch off Stable
Code conventions
Consistent code writing, commenting and documenting style is key to collaboration. Make sure that you read the complete Code conventions section carefully and that your code complies with our guidelines. We are using Laravel coding style as our main style guide.
On code duplication
•	Do not copy-paste source code. Reuse it in a way that makes sense, rewriting the necessary parts.
On indentation
Switch case
Example : (place_holder: add code example)
add code example here
If / else or else if

[ Example : (place_holder: add code example)]
add code example here
On classes
•	The attributes of the class must be protected or private.
•	The Method of the class can be public, private, or protected.
•	Classes can be public or private.
•	Class names must be transparent and representative of their purpose.
•	Class names should be nouns in UpperCamelCase, with the first letter of every word capitalized.
Example : (place_holder: add code example)
add code example here
On variables
•	Local variables, instance variables, and class variables should be written in lowerCamelCase: with the exception of the first world, the first letter of every word should be capitalized.
Example : (place_holder: add code example)
add code example here
On constants
•	Constants should be written in UPPERCASE with words separated by underscores.
Example: (place_holder: add code example)
add code example here
Components architecture
As an automation tester
We will be implementing a continuous integration workflow that will be running multiple automated testing. In the meantime, any experience with CI/CD and automated testing in Laravel is very much welcome. Feel free to contact us at contact@residat.com
On reviewing our code of conduct
Before reviewing and contributing, please make sure to read through our code of conduct.
