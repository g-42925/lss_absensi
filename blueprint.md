# Blueprint - LSS Absensi

## Purpose and Capabilities

LSS Absensi is a web application build with CodeIgniter 3 and integrated with a Flutter-based mobile components. It serves as an attendance management system.

## Project Documentation

- **Framework**: CodeIgniter 3.1.11
- **Design System**: Sneat Bootstrap 5 HTML Admin Template
- **Styling**: Tailwind CSS (CDN), Bootstrap 5
- **Key Features**:
  - Employee attendance tracking.
  - Dashboard with analytics (ApexCharts).
  - Role-based access control.
  - Integration with external APIs (Mapbox, Supabase, etc.).

## Plan for Current Change

### Goal: Fix Endless Tab Loading Issue (Implemented)

The application hangs in a "loading" state specifically in hosting environments during the first visit.

### Steps

1. **Identify Synchronous Blockers**: Found multiple `async: false` AJAX calls in theme assets.
2. **Refactor `main.js`**: Convert the synchronous search typeahead data fetch to asynchronous. (Completed)
3. **Refactor Analytics Scripts**: Convert synchronous chart data fetches in `dashboards-crm.js` and `cards-analytics.js` to asynchronous. (Completed)
4. **Verify**: Ensure the loading state completes and interactive elements still function correctly.
