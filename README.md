# Plugin installation

1. Download the Magento plugin
2. Extract the downloaded ZIP file
3. Upload the extracted folder to your server, on MAGENTO_ROOT/app/code
4. Run the following commands:
	- composer require opennode/opennode-php --ignore-platform-reqs
	- php bin/magento module:enable OpenNode_OpenNodePayment
	- php bin/magento setup:upgrade
	- php bin/magento setup:di:compile
	- php bin/magento cache:clean


# Plugin configuration

After installing the Magento plugin, you should activate it and configure it properly.

You will need an OpenNode API key. To get one, follow these steps:

1. After signing up on OpenNode, log into the platform
2. Click on "Developers" and select the "Integrations" tab
3. Click on the "Generate E-commerce Key" button
4. Copy the generated API Key 

After getting your API key, you can configure your OpenNode Magento plugin:

1. Access your Magento Admin panel.
2. Click on "Stores" and then "Configuration".
3. Click on "Sales" and then "Payment Methods".
4. Select "OpenNode" to view all configuration options
5. Paste the API key in the "API Key" field
