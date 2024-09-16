<h1 style="display: flex; justify-content: space-between; align-items: center;">Welcome to residat-front-platform <img src="public/assets/images/Logos/logo-small.svg" alt="Image Description" height="70"></h1>



[![Version](https://img.shields.io/badge/version-0.0.1-blue.svg?cacheSeconds=2592000)](#)
[![License: agpl](https://img.shields.io/badge/License-agpl-yellow.svg)](#)

![Build Status](https://img.shields.io/badge/build-passing-brightgreen)
![Coverage](https://img.shields.io/badge/coverage-63%25-brightgreen)
![Dependencies](https://img.shields.io/badge/dependencies-up%20to%20date-brightgreen)


<a href="https://backoffice-dev.residat.com">RESIDAT (backoffice)</a> is a digital platform which will allow administrators to have a global view to manage information relating to RESIDAT (frontoffice) thanks to a dashboard. This platform will include several modules
- User management and management
- Post management and management
- management and management of roles and permissions
- Area management
- management and management of different reports

<img src="public\assets\backoffice.png"></img>



## Table of Contents 📚
1. [Introduction](#introduction-🌟)
2. [Context](#context-💡-)
3. [Features](#features-✨)
4. [Project Structure](#project-structure-🏗️)
4. [Project Setup](#project-setup)
5. [Testing](#testing-🧪)
6. [Contributing](#contributing-)
7. [FAQs](#faqs-)
8. [Code of Conduct](#code-of-conduct-)
9. [License](#license-)
10. [Acknowledgments](#acknowledgments-)
11. [Change Log](#change-log-)

## Introduction and Context 🌟

Welcome to <a href="https://backoffice-dev.residat.com">RESIDAT</a>, a backoffice designed to provide a centralized digital platform for administrators to efficiently manage information related to <a href="https://dev.residat.com/community">RESIDAT (frontoffice)</a> through an intuitive dashboard. This platform will encompass several functional modules to address the data management needs of the organization:

- User Management:
Creation, editing, and deletion of user accounts.
Assignment of roles and permissions for granular control over access to features.
- Post Management:
Creation, editing, and deletion of published content.
Management of user comments and interactions.
- Roles and Permissions Management:
Definition of user roles based on responsibilities and authorizations.
Configuration of permissions to restrict or allow access to specific features.
- Zone Management:
Creation and management of geographic zones for efficient hierarchical organization.
- Report Management:
Collection, tracking, and analysis of reports related to RESIDAT.
Visualization of data in a clear and understandable manner to facilitate decision-making.
With RESIDAT, administrators will have access to a comprehensive set of tools to optimize information management and ensure smooth operation of the RESIDAT ecosystem.


 <a href="https://dev.residat.com/community">RESIDAT</a> was born from the necessity to mitigate climate risks for communities in Cameroon. It targets the heart of climate vulnerability by providing critical, actionable data through GIS visualizations and real-time reports. The platform's goal is to empower communities and authorities to make informed decisions, enabling proactive and collaborative efforts towards climate resilience. In the face of increasing climate challenges,  <a href="https://dev.residat.com/community">RESIDAT</a> stands as a beacon of innovation and solidarity.

### Vision Statement
"Residat envisions a resilient Cameroon where every community has the knowledge and tools to adapt to climate variability. Our vision is to become a leading platform in climate risk assessment and adaptation strategies, contributing to sustainable development and disaster risk reduction through innovative geospatial technologies."

### Mission Statement
"Our mission is to empower vulnerable communities in Cameroon by providing them with accurate, accessible, and actionable geospatial data on climate hazards. Through the integration of GIS, drone technology, and big data analytics, Residat aims to facilitate informed decision-making and proactive environmental management."

### Community Statement
"Residat is dedicated to fostering a collaborative environment where scientists, local authorities, developers, and community members come together to combat climate risks. We encourage the sharing of insights, the development of local solutions, and the creation of a united front against the adverse effects of climate change."

### Licensing Strategy
"To maximize impact and encourage innovation, Residat will operate under an AGPL License (Affero General Public License), promoting open-source collaboration. This approach will allow for the free use, modification, and distribution of our resources, ensuring they remain accessible for adaptation to other regions facing similar climate challenges."


## Features ✨

 <a href="https://dev.residat.com/community">RESIDAT</a> offers a powerful suite of features designed to provide stakeholders with comprehensive climate risk data and facilitate community engagement in climate resilience:

- Interactive GIS Dashboards: Leveraging cutting-edge GIS technology,  <a href="https://dev.residat.com/community">RESIDAT</a> provides dynamic maps and graphs that allow users to visualize and interact with climate risk data specific to their local communities.

- Community Intelligence Reports: A dedicated space for stakeholders to publish, manage, and interact with reports on climate adaptation efforts. These reports provide valuable insights into local initiatives and challenges.

- Real-Time Community Chat Rooms: These forums offer a space for stakeholders to discuss climate realities, share observations, and promote climate services, fostering a community-driven approach to climate resilience.

- Mobile Notifications: Integration with mobile platforms ensures that stakeholders receive timely updates and warnings about climate hazards, enabling swift and informed responses to emerging risks.

- Citizen Science Contributions: Encouraging local community members to contribute data and reports,  <a href="https://dev.residat.com/community">RESIDAT</a> amplifies the reach and accuracy of climate risk information through citizen science.

- Data-Driven Insights: By analyzing spatial data and user-contributed reports,  <a href="https://dev.residat.com/community">RESIDAT</a> provides actionable insights that support climate risk management and decision-making processes.

- Stakeholder Engagement Tools: Features designed to enhance collaboration among various actors, including local authorities, NGOs, businesses, and academia, to drive collective action in climate adaptation.


## Project Setup

```sh
composer install
```

### Generate .env file

```sh
copy .env.example .env
```

### Generate key

```sh
php artisan key:generate
```

### Configure Database info 

```sh
DB_DATABASE=
DB_USERNAME=root (default)
DB_PASSWORD=
APP_URL=
```

### Installation and Configuration of Docker (optional)

```sh
docker-compose up -d 
```

### Execute migration and seeder

```sh
php artisan migrate --seed
```

## NOTIFICATION WITH FIREBASE IN .env file

```sh
get your firebase_credentials.json
```

```sh
FIREBASE_CREDENTIALS=/storage/fullpath/firebase_credentials.json (fullpath)
FIREBASE_PROJECT=projet_id
FIREBASE_DATABASE_URL=https://projet_id.firebaseio.com
FIREBASE_STORAGE_DEFAULT_BUCKET=projet_id.appspot.com
```


### Run Unit Tests with [PHPUnit](https://laravel.com/docs/11.x/testing)

### Generate report 

```sh
php artisan test --coverage-html reports/
```



## Testing 🧪

Testing is a crucial aspect of the development process, ensuring that the code is robust and behaves as expected. In the Residat project, we employ PHPUnit for our testing framework, providing an efficient and feature-rich environment for both unit and integration testing. Below is a summary of the current test coverage and details about the testing setup and examples.

### Test Coverage Summary

<!-- <img src="public\assets\images\Documentation\couverageView.jpeg"></img> -->

The test coverage report provides valuable insight into the robustness of our test suite. Here's a brief overview:

- **Overall Coverage**: Approximately `65.67%` of statements and 69.58% of branches are covered by tests.


## FAQs ❓

// Answers to frequently asked questions.


## License ⚖️

This file is part of MapAndRank - Residat.
 
 * MapAndRank - Residat is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * MapAndRank - Residat is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MapAndRank - Residat. If not, see <https://www.gnu.org/licenses/>.

## Acknowledgments 🙏

// Credits to contributors and special mentions.

## Change Log 📝

// Log of changes and version history.

## Documentation

https://backend-doc.residat.com/#introduction