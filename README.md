
# DNA Distribution Customer Resources Portal

A secure web portal for DNA Distribution customers to access shared resources and documentation through a centralized interface.

## Features

- Secure authentication via Shopify customer accounts
- Google Drive integration for document management
- Dark theme UI with custom styling
- File preview support for various formats
- Breadcrumb navigation
- Responsive design for mobile and desktop

## Technical Stack

- PHP 8.2
- Bootstrap 5.3
- Google Drive API
- Shopify API Integration
- Font Awesome Icons

## Environment Setup

The following environment variables need to be configured:

```
SHOPIFY_API_KEY=
SHOPIFY_API_PASSWORD= 
SHOPIFY_API_URL=
GOOGLE_APPLICATION_CREDENTIALS=
GOOGLE_DRIVE_ROOT_FOLDER=
GOOGLE_DRIVE_FOLDER_ID=
GOOGLE_DRIVE_IS_SHARED=
```

## Security

- Secure file access through Google Drive permissions
- Authentication required for all resource access
- Environment variables for sensitive credentials

## Browser Support

The portal is tested and supported on:
- Chrome/Edge (latest versions)
- Firefox (latest version)
- Safari (latest version)

## Deployment

The application is designed to run on Replit's infrastructure and can be deployed directly through the Replit platform.

## License

Proprietary software for DNA Distribution. All rights reserved.
