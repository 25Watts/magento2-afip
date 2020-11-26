# Magento 2 Watts25 Afip

This Extension is used to manage electronic billing between Magento 2 and AFIP in Argentina

## Features:

### Backend
- Allow end user to generate electronic billing from Magento dashboard
- Allow end user to input the cuit number from Magento dashboard
- configuration for enable and disable extension
- setting cuit number

## Introduction installation:

### Install Magento 2 Watts25 Afip
- Create a folder [root]/app/code/Watts25/Afip
- Copy to folder

### Enable Extension

```
php bin/magento module:enable Watts25_Afip
php bin/magento setup:upgrade
php bin/magento cache:clean
php bin/magento setup:static-content:deploy
```