# Vendor
This Phase 1 build includes the deployment autoload scaffold. The Google API PHP Client is declared in composer.json but could not be fetched in the offline build environment. Before production packaging, the build environment must run Composer to populate the complete vendor tree; the target Hostinger server does not need Composer.
