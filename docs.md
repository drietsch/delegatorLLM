# Pimcore Platform Documentation


## platform

Welcome to the Pimcore Development documentation. The Pimcore platform consists of the Pimcore Core Framework

- [Pimcore Development Documentation](/index.md): Welcome to the Pimcore Development documentation. The Pimcore platform consists of the Pimcore Core Framework

### search

- [Search the documentation](/search.md)

### Advanced_Object_Search

Advanced Object Search bundle provides advanced object search in

- [Pimcore Advanced Object Search via OpenSearch or Elasticsearch](/Advanced_Object_Search.md): Advanced Object Search bundle provides advanced object search in

#### Elasticsearch

This bundle requires minimum version of Elasticsearch 8.0.

- [Elasticsearch Client Setup](/Advanced_Object_Search/Elasticsearch.md): This bundle requires minimum version of Elasticsearch 8.0.

#### Exclude_Fields

It is possible to exclude specified fields from the search index by extending the services.yaml:

- [Exclude Fields](/Advanced_Object_Search/Exclude_Fields.md): It is possible to exclude specified fields from the search index by extending the services.yaml:

#### Extending_Filters

The advanced object search enables the definition of custom search filters that can be used to filter the result. This

- [Extending Filters](/Advanced_Object_Search/Extending_Filters.md): The advanced object search enables the definition of custom search filters that can be used to filter the result. This

#### Installation

This bundle is only supported on Pimcore Core Framework 11.

- [Installation of Advanced Object Search](/Advanced_Object_Search/Installation.md): This bundle is only supported on Pimcore Core Framework 11.

#### Opensearch

This bundle requires minimum version of OpenSearch 2.7.

- [OpenSearch Client Setup](/Advanced_Object_Search/Opensearch.md): This bundle requires minimum version of OpenSearch 2.7.

#### Upgrade_Notes

Upgrade to v3.0.0

- [Upgrade Notes](/Advanced_Object_Search/Upgrade_Notes.md): Upgrade to v3.0.0

### Backend_Power_Tools

Backend Power Tools provides additional tools for Pimcore backend to ease everyday tasks.

- [Pimcore Backend Power Tools](/Backend_Power_Tools.md): Backend Power Tools provides additional tools for Pimcore backend to ease everyday tasks.

#### Alternative_Element_Trees

The Alternative Element Tree functionality allows you to create alternative views of your element trees, sorting your data by the attributes that matter the most for your project.

- [Alternative Element Trees](/Backend_Power_Tools/Alternative_Element_Trees.md): The Alternative Element Tree functionality allows you to create alternative views of your element trees, sorting your data by the attributes that matter the most for your project.

##### Customization

- [Create a Custom Backend Adapter](/Backend_Power_Tools/Alternative_Element_Trees/Customization/Adding_Custom_Backend_Adapter.md): To create a custom backend adapter, you need to follow two steps:
- [Create a Custom Frontend Adapter](/Backend_Power_Tools/Alternative_Element_Trees/Customization/Adding_Custom_Frontend_Adapter.md): To create a custom frontend adapter, you need to follow three steps:
- [Extending Adapters](/Backend_Power_Tools/Alternative_Element_Trees/Customization/Extending_Adapters.md): The bundle already implements some adapters for special data types (e.g. objectbrick to keep only specific fields). You can override the core adapters (e.g. input or numeric) with your own classes and add custom ones when needed.
- [Precondition Filter](/Backend_Power_Tools/Alternative_Element_Trees/Customization/Precondition_Filter.md): The precondition filter is used to reduce the amount of objects that will be saved in the alternative element tree.

##### Work_With_AET

- [Command Lines](/Backend_Power_Tools/Alternative_Element_Trees/Work_With_AET/Command_Lines.md): This page lists the available command lines for Alternative Element Trees. You can also access this list by typing bpt:aet.
- [Alternative Element Trees Configuration](/Backend_Power_Tools/Alternative_Element_Trees/Work_With_AET/Configuration.md): At the moment, the Alternative Element Trees are only available for Data Objects.
- [Alternative Element Tree Visualization](/Backend_Power_Tools/Alternative_Element_Trees/Work_With_AET/Visualization.md): This page explains the alternative views provided by alternative element trees.

#### Bookmark_Lists

This functionality of the Backend Power Tools bundle allows you to create bookmark lists for Pimcore elements and to share them with other users.

- [Bookmark Lists](/Backend_Power_Tools/Bookmark_Lists.md): This functionality of the Backend Power Tools bundle allows you to create bookmark lists for Pimcore elements and to share them with other users.

##### Share_Bookmark_Lists

You can share a bookmark list with other Pimcore users or roles from the Bookmark Lists grid by clicking the Share Bookmark List Share icon.

- [Share a Bookmark List](/Backend_Power_Tools/Bookmark_Lists/Share_Bookmark_Lists.md): You can share a bookmark list with other Pimcore users or roles from the Bookmark Lists grid by clicking the Share Bookmark List Share icon.

##### Working_with_Bookmark_Lists

The Bookmark Lists functionality can be accessed from the sidebar menu in the File > Bookmark Lists section.

- [Work with Bookmark Lists](/Backend_Power_Tools/Bookmark_Lists/Working_with_Bookmark_Lists.md): The Bookmark Lists functionality can be accessed from the sidebar menu in the File > Bookmark Lists section.

#### Installation

This bundle is only supported on Pimcore Core Framework 11.

- [Installation of the Backend Power Tools Bundle](/Backend_Power_Tools/Installation.md): This bundle is only supported on Pimcore Core Framework 11.

### Copilot_Showcases

The Pimcore Copilot Showcases serve as a collection of action steps and examples to demonstrate the capabilities of the

- [Copilot Showcases](/Copilot_Showcases.md): The Pimcore Copilot Showcases serve as a collection of action steps and examples to demonstrate the capabilities of the

#### Included_Actions

The following actions and action parts are included in the bundle and can be adapted to your specific needs.

- [Included Actions](/Copilot_Showcases/Included_Actions.md): The following actions and action parts are included in the bundle and can be adapted to your specific needs.

##### AI_Integrations_Powered_By_Hugging_Face

- [Hugging Face Fine-tune Models](/Copilot_Showcases/Included_Actions/AI_Integrations_Powered_By_Hugging_Face/Hugging_Face_Fine-tune_Models.md): The following action steps utilize Pimcore Fine-Tuning Service
- [Hugging Face Image To Image Prompt](/Copilot_Showcases/Included_Actions/AI_Integrations_Powered_By_Hugging_Face/Hugging_Face_Image_to_Image_Prompt.md): This Action generates images using AI models based on an already existing image.

##### Custom_Reports

This action provides a shortcut to open and view custom reports in the Pimcore backend.

- [Custom Reports](/Copilot_Showcases/Included_Actions/Custom_Reports.md): This action provides a shortcut to open and view custom reports in the Pimcore backend.

##### Link_To_Parent

This actions links one or more data objects to a new parent object.

- [Link To Parent](/Copilot_Showcases/Included_Actions/Link_To_Parent.md): This actions links one or more data objects to a new parent object.

#### Installation

This bundle is only supported on Pimcore Core Framework 11.

- [Installation of the Copilot Showcase Bundle](/Copilot_Showcases/Installation.md): This bundle is only supported on Pimcore Core Framework 11.

### Copilot

The Pimcore Copilot serves as the central starting point for executing various actions within the Pimcore backend.

- [Pimcore Copilot](/Copilot.md): The Pimcore Copilot serves as the central starting point for executing various actions within the Pimcore backend.

#### Configuration

In general, Pimcore Copilot lists all actions which are registered, details see

- [Configuration](/Copilot/Configuration.md): In general, Pimcore Copilot lists all actions which are registered, details see

##### Automation_Actions

Automation actions allow you to configure actions that consist of one or multiple steps and will be executed

- [Automation Actions](/Copilot/Configuration/Automation_Actions.md): Automation actions allow you to configure actions that consist of one or multiple steps and will be executed
- [Configuration via Configuration File](/Copilot/Configuration/Automation_Actions/Configuration_via_File.md): Configuration takes place via LocationAwareConfigRepository, and can be done via YAML config or settings store.
- [Environment Variables](/Copilot/Configuration/Automation_Actions/Environment_Variables.md): Environment Variables are used in Automation Actions to define values that a user has to enter before starting an action.

##### Generic_Data_Index

Some actions make use of the generic data index bundle to retrieve data.

- [Generic Data Index Configuration](/Copilot/Configuration/Generic_Data_Index.md): Some actions make use of the generic data index bundle to retrieve data.

##### Http_Clients

Copilot uses two different scoped Symfony HttpClient component to send HTTP requests.

- [HTTP Clients](/Copilot/Configuration/Http_Clients.md): Copilot uses two different scoped Symfony HttpClient component to send HTTP requests.

##### Interaction_Actions

Interaction actions are actions in Pimcore Copilot, that allow an interaction between editor and copilot in form of

- [Interaction Actions](/Copilot/Configuration/Interaction_Actions.md): Interaction actions are actions in Pimcore Copilot, that allow an interaction between editor and copilot in form of
- [Configuration via Configuration File](/Copilot/Configuration/Interaction_Actions/Configuration_via_File.md): Configuration takes place via LocationAwareConfigRepository, and can be done via YAML config or settings store.

##### Permissions

Permissions for Pimcore Copilot are defined on two levels:

- [Permissions](/Copilot/Configuration/Permissions.md): Permissions for Pimcore Copilot are defined on two levels:

#### Copilot_Window

When starting the Pimcore Copilot with Alt + X or your configured key binding, you will see all actions that

- [Pimcore Copilot Window](/Copilot/Copilot_Window.md): When starting the Pimcore Copilot with Alt + X or your configured key binding, you will see all actions that

#### Extending_Copilot

Pimcore Copilot Bundle consists of four main modules

- [Extending Pimcore Copilot](/Copilot/Extending_Copilot.md): Pimcore Copilot Bundle consists of four main modules

##### Contexts

Contextual awareness is a crucial part of Pimcore Copilot. Therefore, Pimcore Copilot can filter available actions for

- [Contexts](/Copilot/Extending_Copilot/Contexts.md): Contextual awareness is a crucial part of Pimcore Copilot. Therefore, Pimcore Copilot can filter available actions for

##### Custom_Frontend_Actions

In order to add functionality that specifically is needed only in the UI, you can use the generic handlerAdapter.

- [Custom Frontend Actions](/Copilot/Extending_Copilot/Custom_Frontend_Actions.md): In order to add functionality that specifically is needed only in the UI, you can use the generic handlerAdapter.

##### Custom_Yaml_Validation

In order to prevent unwanted input in your Yaml configuration, you can add specific validation rules.

- [Custom Yaml Validation](/Copilot/Extending_Copilot/Custom_Yaml_Validation.md): In order to prevent unwanted input in your Yaml configuration, you can add specific validation rules.

##### Extending_Automation_Actions

There are several options to customize and extend automation actions:

- [Extending Automation Actions](/Copilot/Extending_Copilot/Extending_Automation_Actions.md): There are several options to customize and extend automation actions:
- [Environment Variable Transformers](/Copilot/Extending_Copilot/Extending_Automation_Actions/Environment_Variable_Transformers.md): Transformers are used to modify matrix values before they are processed by automation actions. Currently, only
- [Extended Error Handling](/Copilot/Extending_Copilot/Extending_Automation_Actions/Extended_Error_Handling.md): Handling exceptions during execution
- [Implement Custom Environment Variable Types](/Copilot/Extending_Copilot/Extending_Automation_Actions/Implement_Custom_Environment_Variable_Types.md): To create a custom environment variable type, create a class that extends
- [Implement Custom Job Steps](/Copilot/Extending_Copilot/Extending_Automation_Actions/Implement_Custom_Job_Steps.md): Job step implementations contain the actual business logic that can be executed via automation actions, e.g. generating

##### Extending_Interaction_Actions

Implementing Interaction Types

- [Extending Interaction Actions](/Copilot/Extending_Copilot/Extending_Interaction_Actions.md): Implementing Interaction Types

##### Extending_Twig_Configuration

Twig is the template engine used by Copilot in a lot of places.

- [Extending Twig Configuration](/Copilot/Extending_Copilot/Extending_Twig_Configuration.md): Twig is the template engine used by Copilot in a lot of places.

##### Precondition_Filter

Precondition filters are used to filter the list of available element when a scheduled task is executed. This is useful if you want to limit the list of elements that should be processed by a scheduled task.

- [Precondition Filter](/Copilot/Extending_Copilot/Precondition_Filter.md): Precondition filters are used to filter the list of available element when a scheduled task is executed. This is useful if you want to limit the list of elements that should be processed by a scheduled task.

##### Regular_Actions

To register regular actions in Pimcore Copilot, two components need to be implemented. For actual examples see shipped

- [Register Regular Actions to Pimcore Copilot](/Copilot/Extending_Copilot/Regular_Actions.md): To register regular actions in Pimcore Copilot, two components need to be implemented. For actual examples see shipped

#### Included_Actions

The following actions and action parts are included in the bundle and can be adapted to your specific needs.

- [Included Actions](/Copilot/Included_Actions.md): The following actions and action parts are included in the bundle and can be adapted to your specific needs.

##### AI_Integrations_Powered_By_Hugging_Face

The following actions are included in the bundle and can be adapted to your specific needs.

- [AI Integrations Powered By Hugging Face](/Copilot/Included_Actions/AI_Integrations_Powered_By_Hugging_Face.md): The following actions are included in the bundle and can be adapted to your specific needs.
- [Hugging Face Automated Translation](/Copilot/Included_Actions/AI_Integrations_Powered_By_Hugging_Face/Hugging_Face_Automated_Translation.md): This Action enables the translation of text within Assets and Data Objects, either as a standalone process or as part of an automation pipeline (e.g., after generating text).
- [Hugging Face Image Classification](/Copilot/Included_Actions/AI_Integrations_Powered_By_Hugging_Face/Hugging_Face_Image_Classification.md): This action can be executed on an image level and lets you automatically send selected images to a configurable Hugging Face endpoint to classify the images by using tags.
- [Hugging Face Image To Text](/Copilot/Included_Actions/AI_Integrations_Powered_By_Hugging_Face/Hugging_Face_Image_To_Text.md): This action can be executed on an asset level and lets you automatically send selected assets to a configurable Hugging Face endpoint to extract text from the images.
- [Hugging Face Image Up Scaling](/Copilot/Included_Actions/AI_Integrations_Powered_By_Hugging_Face/Hugging_Face_Image_Up_Scaling.md): This action allows you to select a single or multiple images from the assets and upscale by a factor of 2. The generated images will update the selected assets or create new assets with a separate suffix.
- [Hugging Face Text Classification](/Copilot/Included_Actions/AI_Integrations_Powered_By_Hugging_Face/Hugging_Face_Text_Classification.md): This action can be executed on data object level lets
- [Hugging Face Text Generation](/Copilot/Included_Actions/AI_Integrations_Powered_By_Hugging_Face/Hugging_Face_Text_Generation.md): This Action facilitates the generation of text using AI models based on a specified prompt template (twig) and relevant attributes or metadata of Assets and Data Objects.
- [Hugging Face Text to Image (One Shot)](/Copilot/Included_Actions/AI_Integrations_Powered_By_Hugging_Face/Hugging_Face_Text_to_Image.md): This Action allows generating images using AI models, based on specified fields within a Data Object, and saves these generated images to an Image field in the same Data Object.
- [Hugging Face Text to Image (Prompt)](/Copilot/Included_Actions/AI_Integrations_Powered_By_Hugging_Face/Hugging_Face_Text_to_Image_Prompt.md): This Action allows generating images using AI models, based on specified fields within a Data Object.
- [Hugging Face Translation Prompt](/Copilot/Included_Actions/AI_Integrations_Powered_By_Hugging_Face/Hugging_Face_Translation_Prompt.md): This Translation action allows for immediate, direct text translations across all configured language pairs without the need for asynchronous processes.
- [Hugging Face Zero Shot Image Classification](/Copilot/Included_Actions/AI_Integrations_Powered_By_Hugging_Face/Hugging_Face_Zero_Shot_Image_Classification.md): This action can be executed on an image level lets you automatically send selected images to a configurable Hugging Face endpoint to classify the images according to the configured Tags in pimcore.
- [Hugging Face Zero Shot Text Classification](/Copilot/Included_Actions/AI_Integrations_Powered_By_Hugging_Face/Hugging_Face_Zero_Shot_Text_Classification.md): This action can be executed on data object level lets

##### AI_Text_Generation

This document covers the OpenAI-compatible text generation automation action step available in Pimcore Copilot.

- [OpenAI-Compatible Text Generation](/Copilot/Included_Actions/AI_Text_Generation.md): This document covers the OpenAI-compatible text generation automation action step available in Pimcore Copilot.

##### Asset_Relation_Assignment

This action can be executed on an asset level and lets you automatically assign images to data objects based on

- [Asset Relation Assignment](/Copilot/Included_Actions/Asset_Relation_Assignment.md): This action can be executed on an asset level and lets you automatically assign images to data objects based on

##### Assign_Execution_Context_To_Asset

This automation action allows you to assign the execution context to asset metadata. It can be configured to assign the data from previous steps to the asset using the metadata.

- [Assign Execution Context To Asset Metadata](/Copilot/Included_Actions/Assign_Execution_Context_To_Asset.md): This automation action allows you to assign the execution context to asset metadata. It can be configured to assign the data from previous steps to the asset using the metadata.

##### Assign_Execution_Context_To_Data_Object

This automation action allows you to assign the execution context to data objects. It can be configured to assign the data from previous steps to the data object.

- [Assign Execution Context To Data Objects](/Copilot/Included_Actions/Assign_Execution_Context_To_Data_Object.md): This automation action allows you to assign the execution context to data objects. It can be configured to assign the data from previous steps to the data object.

##### Change_Workflow_State

This automation action allows to apply a transition to a workflow of one or multiple elements.

- [Apply workflow transition](/Copilot/Included_Actions/Change_Workflow_State.md): This automation action allows to apply a transition to a workflow of one or multiple elements.

##### Filter_Assets

This action can be executed as step to filter assets based on the Pimcore Query Language (PQL).

- [Filter Assets](/Copilot/Included_Actions/Filter_Assets.md): This action can be executed as step to filter assets based on the Pimcore Query Language (PQL).

##### Filter_Data_Objects

This action can be executed as step to filter data objects based on the Pimcore Query Language (PQL).

- [Filter Data Objects](/Copilot/Included_Actions/Filter_Data_Objects.md): This action can be executed as step to filter data objects based on the Pimcore Query Language (PQL).

##### OpenAI_Text_Generation

This interaction type can be executed on an object context (events) and lets you create texts for data objects and refine the

- [Text Generation with Prompts via OpenAI](/Copilot/Included_Actions/OpenAI_Text_Generation.md): This interaction type can be executed on an object context (events) and lets you create texts for data objects and refine the

##### Send_Notification_Email

This automation action allows to send a notification or email to specified users. It can be configured to send the data from previous steps or step environment variables.

- [Send Notification and/or Email](/Copilot/Included_Actions/Send_Notification_Email.md): This automation action allows to send a notification or email to specified users. It can be configured to send the data from previous steps or step environment variables.

##### Variant_Generator

This action step can be executed on an object context and lets you create variants of

- [Variant Generator](/Copilot/Included_Actions/Variant_Generator.md): This action step can be executed on an object context and lets you create variants of

##### Webhook

This automation action allows to send an HTTP request to a given URL. The request can be configured to send data from

- [Webhook](/Copilot/Included_Actions/Webhook.md): This automation action allows to send an HTTP request to a given URL. The request can be configured to send data from

#### Installation

Installation

- [Installation of Pimcore Copilot](/Copilot/Installation.md): Installation

#### Job_Overview

The Job Overview page can be opened via Settings -> Pimcore Copilot -> Pimcore Copilot  Job Runs and contains an overview about

- [Job Overview](/Copilot/Job_Overview.md): The Job Overview page can be opened via Settings -> Pimcore Copilot -> Pimcore Copilot  Job Runs and contains an overview about

#### Logging

The CoPilot bundle leverages Symfony's default Monolog logging to record messages under the copilot channel.

- [Logging](/Copilot/Logging.md): The CoPilot bundle leverages Symfony's default Monolog logging to record messages under the copilot channel.

#### ResponseMappingService

The ResponseMappingService provides a flexible way to extract values from JSON API responses using Symfony Expression Language. This service addresses the problem of inconsistent response structures across different model endpoints.

- [ResponseMappingService](/Copilot/ResponseMappingService.md): The ResponseMappingService provides a flexible way to extract values from JSON API responses using Symfony Expression Language. This service addresses the problem of inconsistent response structures across different model endpoints.

#### Upgrade_Notes

1.1.0 to 2.0.0

- [Upgrade notes](/Copilot/Upgrade_Notes.md): 1.1.0 to 2.0.0

##### Migrate_From_JEE_To_GEE

Introduction

- [Migrate from Job Execution Engine to Generic Execution Engine](/Copilot/Upgrade_Notes/Migrate_From_JEE_To_GEE.md): Introduction

### Customer_Management_Framework

Pimcore allows to manage any kind of data - unstructured, digital assets and structured content. The most obvious structured content is product data and all data related to products like categories, technologies, brands, etc.

- [Pimcore Customer Management Framework](/Customer_Management_Framework.md): Pimcore allows to manage any kind of data - unstructured, digital assets and structured content. The most obvious structured content is product data and all data related to products like categories, technologies, brands, etc.

#### ActionTrigger

The CMF plugin offers tools to automate customer related actions based on rules. Every rule consists of one or more

- [ActionTrigger Services - Customer automation rules](/Customer_Management_Framework/ActionTrigger.md): The CMF plugin offers tools to automate customer related actions based on rules. Every rule consists of one or more

#### Activities

An important part of the customer management framework are customer activities. They can be pretty much every activity a

- [Activities](/Customer_Management_Framework/Activities.md): An important part of the customer management framework are customer activities. They can be pretty much every activity a

#### Architecture-Overview

The following architecture overview shows available framework components. The component configuration takes place in the

- [Architecture Overview](/Customer_Management_Framework/Architecture-Overview.md): The following architecture overview shows available framework components. The component configuration takes place in the

#### Configuration

Being a framework, there are a lot of settings in the CMF. These settings can be configured in a combination of

- [Configuration of CMF](/Customer_Management_Framework/Configuration.md): Being a framework, there are a lot of settings in the CMF. These settings can be configured in a combination of

#### Cronjobs

Following is a list of all cronjobs needed by the CMF bundle. Depending on the project requirements, the execution interval

- [CronJobs](/Customer_Management_Framework/Cronjobs.md): Following is a list of all cronjobs needed by the CMF bundle. Depending on the project requirements, the execution interval

#### CustomerDuplicatesService

The CMF ships with a Customer Duplicates Service which helps to find, merge and avoid duplicate entries. It consists of

- [Customer Duplicates Service](/Customer_Management_Framework/CustomerDuplicatesService.md): The CMF ships with a Customer Duplicates Service which helps to find, merge and avoid duplicate entries. It consists of

#### CustomerSaveManager

The customer save manager is responsible for all actions/hooks which are executed when a customer object is saved.

- [Customer Save Manager](/Customer_Management_Framework/CustomerSaveManager.md): The customer save manager is responsible for all actions/hooks which are executed when a customer object is saved.

#### CustomerSegments

*Customer segmentation is the practice of dividing a customer base into groups of individuals that are similar in specific

- [Customer Segments](/Customer_Management_Framework/CustomerSegments.md): *Customer segmentation is the practice of dividing a customer base into groups of individuals that are similar in specific

#### Installation

This section describes the installation of the Customer Management Framework and the first steps of configuration.

- [Installation and First Configuration](/Customer_Management_Framework/Installation.md): This section describes the installation of the Customer Management Framework and the first steps of configuration.

##### Update

Update to Version 5.0

- [Update Notices](/Customer_Management_Framework/Installation/Update.md): Update to Version 5.0

#### ListViews

The CMF provides two additional views for visualizing the data in Pimcore backend UI.

- [List Views](/Customer_Management_Framework/ListViews.md): The CMF provides two additional views for visualizing the data in Pimcore backend UI.

#### NewsletterSync

The CMF offers built-in support for synchronizing customer data with MailChimp. It synchronizes configured parts of the

- [MailChimp/Newsletter Sync](/Customer_Management_Framework/NewsletterSync.md): The CMF offers built-in support for synchronizing customer data with MailChimp. It synchronizes configured parts of the

##### LinkActivityTracking

The CMF provides you with features to track opened links of newsletters as customer activities. So you can track activities

- [Link Activity Tracking](/Customer_Management_Framework/NewsletterSync/LinkActivityTracking.md): The CMF provides you with features to track opened links of newsletters as customer activities. So you can track activities

##### MultipleMailchimpAccounts

Optionally it's possible to configure the service container to be able to handle multiple mailchimp accounts. Principally it's handled the same way like newsletter lists via mailchimp provider handler services (see Configuration for an example definition)*[]:

- [Multiple Mailchimp Accounts](/Customer_Management_Framework/NewsletterSync/MultipleMailchimpAccounts.md): Optionally it's possible to configure the service container to be able to handle multiple mailchimp accounts. Principally it's handled the same way like newsletter lists via mailchimp provider handler services (see Configuration for an example definition)*[]:

#### Personalization

The CMF is tightly integrated into the Pimcore personalization feature and connects the personalization for anonymous

- [Integration with Pimcore Targeting Engine](/Customer_Management_Framework/Personalization.md): The CMF is tightly integrated into the Pimcore personalization feature and connects the personalization for anonymous

##### Additional_Action_Trigger_Service_Components

In addition to the Pimcore Global Targeting Rules there are also additional triggers, conditions and actions for integration

- [Special Triggers, Conditions and Actions in CMF Action Trigger Service](/Customer_Management_Framework/Personalization/Additional_Action_Trigger_Service_Components.md): In addition to the Pimcore Global Targeting Rules there are also additional triggers, conditions and actions for integration

##### Additional_Targeting_Role_Components

The CMF adds following conditions and action to the Pimcore Global Targeting Rules:

- [Additional Conditions and Actions to Pimcore Global Targeting Rules](/Customer_Management_Framework/Personalization/Additional_Targeting_Role_Components.md): The CMF adds following conditions and action to the Pimcore Global Targeting Rules:

##### Example_Usecases

Following examples describe typical usecases for the Pimcore personalization engine in combination with the CMF.

- [Example Usecases](/Customer_Management_Framework/Personalization/Example_Usecases.md): Following examples describe typical usecases for the Pimcore personalization engine in combination with the CMF.

#### SegmentAssignment

In addition to customers, it is also possible to assign segments to other Pimcore elements. So, elements like documents

- [Assigning Segments to Pimcore Elements](/Customer_Management_Framework/SegmentAssignment.md): In addition to customers, it is also possible to assign segments to other Pimcore elements. So, elements like documents

#### Webservice

The CMF plugin has a built-in REST webservice. Access is handled via API-Keys, which can be configured

- [Webservice](/Customer_Management_Framework/Webservice.md): The CMF plugin has a built-in REST webservice. Access is handled via API-Keys, which can be configured

#### Working-with-Customers

As already described in the installation chapter, customers are simple data objects that need to implement certain

- [Working with Customers](/Customer_Management_Framework/Working-with-Customers.md): As already described in the installation chapter, customers are simple data objects that need to implement certain

### Data_Importer

This extension adds a comprehensive import functionality to Pimcore Datahub. It allows importing data from external

- [Pimcore Data Importer](/Data_Importer.md): This extension adds a comprehensive import functionality to Pimcore Datahub. It allows importing data from external

#### Configuration

This section gives your details about all the configuration options of an import. Also, have a look

- [Configuration](/Data_Importer/Configuration.md): This section gives your details about all the configuration options of an import. Also, have a look

##### Data_Sources

Every data importer configuration needs a data source. Following data sources are available and can

- [Data Sources](/Data_Importer/Configuration/Data_Sources.md): Every data importer configuration needs a data source. Following data sources are available and can

##### Execution_Configuration

Depending on the configuration there are different options to start an actual import.

- [Execution Configuration](/Data_Importer/Configuration/Execution_Configuration.md): Depending on the configuration there are different options to start an actual import.

##### File_Formats

The source data needs to be in an interpretable format for the importer. Following interpreters can

- [File Formats](/Data_Importer/Configuration/File_Formats.md): The source data needs to be in an interpretable format for the importer. Following interpreters can

##### Import_Preview

The import configuration allows to show a preview of the data which helps

- [Import Preview](/Data_Importer/Configuration/Import_Preview.md): The import configuration allows to show a preview of the data which helps

##### Mapping_Configuration

The mapping configuration defines what data fields from the import data should be imported where and how to

- [Mapping Configuration](/Data_Importer/Configuration/Mapping_Configuration.md): The mapping configuration defines what data fields from the import data should be imported where and how to
- [Data Target](/Data_Importer/Configuration/Mapping_Configuration/Data_Target.md): Data target definition assigns the result of the transformation pipeline to a data object field. The available data object
- [Classification Store Batch Details](/Data_Importer/Configuration/Mapping_Configuration/Data_Target/Classification_Store_Batch_Details.md): The Classification Store Batch data target allows assigning multiple classification store attributes
- [Transformation Pipeline](/Data_Importer/Configuration/Mapping_Configuration/Transformation_Pipeline.md): The transformation pipeline takes the data read from source data based on the

##### Processing_Settings

There are a couple of processing settings that allow fine-tuning the import process.

- [Processing Settings](/Data_Importer/Configuration/Processing_Settings.md): There are a couple of processing settings that allow fine-tuning the import process.

##### Resolver_Settings

Resolver settings are responsible to define to which Pimcore data imported data should

- [Resolver Settings](/Data_Importer/Configuration/Resolver_Settings.md): Resolver settings are responsible to define to which Pimcore data imported data should

#### Extending

The bundle architecture provides several extension possibilities for developers to customize the behaviour.

- [Extending](/Data_Importer/Extending.md): The bundle architecture provides several extension possibilities for developers to customize the behaviour.

##### Events

Another option to customize import behavior is listening for events. The bundle fires following events during the

- [Events](/Data_Importer/Extending/Events.md): Another option to customize import behavior is listening for events. The bundle fires following events during the

##### Extend_Custom_Strategies

The bundle architecture easily allows extension and customization of many

- [Extend via Custom Strategies](/Data_Importer/Extending/Extend_Custom_Strategies.md): The bundle architecture easily allows extension and customization of many

#### Import_Execution_Details

Every import execution consists of two major parts

- [Import Execution Details](/Data_Importer/Import_Execution_Details.md): Every import execution consists of two major parts

#### Import_Progress_and_Logging

Import Progress

- [Import Progress and Logging](/Data_Importer/Import_Progress_and_Logging.md): Import Progress

#### Installation

Required Bundles

- [Installation](/Data_Importer/Installation.md): Required Bundles

#### Troubleshooting_FAQ

1) Status in the Execution isnÂ´t progressing

- [Troubleshooting / FAQ](/Data_Importer/Troubleshooting_FAQ.md): 1) Status in the Execution isnÂ´t progressing

#### Upgrade

Update to Version 1.11

- [Update Notes](/Data_Importer/Upgrade.md): Update to Version 1.11

### Data_Quality_Management

The Pimcore Data Quality Management bundle allows you to track and visualize your data quality.

- [Pimcore Data Quality Management](/Data_Quality_Management.md): The Pimcore Data Quality Management bundle allows you to track and visualize your data quality.

#### Customization


##### Calculation_Rules

You can configure the data quality steps according to your needs.

- [Calculation Rules](/Data_Quality_Management/Customization/Calculation_Rules.md): You can configure the data quality steps according to your needs.

##### Custom_Rule_Definition

To create a custom rule definition, you need to follow four steps:

- [Custom Rule Definition](/Data_Quality_Management/Customization/Custom_Rule_Definition.md): To create a custom rule definition, you need to follow four steps:

##### Custom_Score_Badges

Pimcore provides three CSS classes you can use for styling the scores in the tree.

- [Custom Score Badges](/Data_Quality_Management/Customization/Custom_Score_Badges.md): Pimcore provides three CSS classes you can use for styling the scores in the tree.

#### Data_Quality_Configuration

This page explains how and where to configure data quality scores for your Data Objects.

- [Data Quality Configuration](/Data_Quality_Management/Data_Quality_Configuration.md): This page explains how and where to configure data quality scores for your Data Objects.

#### Installation

This bundle is only supported on Pimcore Core Framework 11.

- [Installation](/Data_Quality_Management/Installation.md): This bundle is only supported on Pimcore Core Framework 11.

#### Visualization

Once you add a data quality data type to your Data Object class, the score computed for your Data Objects is visible:

- [Data Quality Visualization](/Data_Quality_Management/Visualization.md): Once you add a data quality data type to your Data Object class, the score computed for your Data Objects is visible:

### Datahub_File_Export

This extension allows the publishing of the data stored in Pimcore automatically to flat CSV, XML, or JSON files

- [Pimcore Datahub File Export](/Datahub_File_Export.md): This extension allows the publishing of the data stored in Pimcore automatically to flat CSV, XML, or JSON files

#### Basic_Configuration

General settings

- [Basics](/Datahub_File_Export/Basic_Configuration.md): General settings

#### Customize_and_Extend

There are multiple ways for extending the file export adapter.

- [Customize and Extend](/Datahub_File_Export/Customize_and_Extend.md): There are multiple ways for extending the file export adapter.

#### Events

There are a couple of events which you can use to modify the export.

- [File exporter - Events](/Datahub_File_Export/Events.md): There are a couple of events which you can use to modify the export.

#### Export_Setup

The bundle allows manual and automatic execution of exports. The export can be setup in different ways. For all ways,

- [Export setup](/Datahub_File_Export/Export_Setup.md): The bundle allows manual and automatic execution of exports. The export can be setup in different ways. For all ways,

#### Installation

Required Bundles

- [Installation](/Datahub_File_Export/Installation.md): Required Bundles

##### Upgrade

Following steps are necessary during updating to newer versions.

- [Upgrade Information](/Datahub_File_Export/Installation/Upgrade.md): Following steps are necessary during updating to newer versions.

### Datahub_Productsup

This bundle integrates product data syndication platform productsup to Pimcore Datahub

- [Pimcore DataHub Productsup](/Datahub_Productsup.md): This bundle integrates product data syndication platform productsup to Pimcore Datahub

#### Basic_Configuration

General settings

- [Basic configuration](/Datahub_Productsup/Basic_Configuration.md): General settings

#### Events

There are a couple of events which you can use to modify the exported data or to modify the imported order objects.

- [Events](/Datahub_Productsup/Events.md): There are a couple of events which you can use to modify the exported data or to modify the imported order objects.

#### Installation

Required Bundles

- [Installation](/Datahub_Productsup/Installation.md): Required Bundles

##### Upgrade

Following steps are necessary during updating to newer versions.

- [Upgrade Information](/Datahub_Productsup/Installation/Upgrade.md): Following steps are necessary during updating to newer versions.

#### Setup_Delivery

The delivery endpoint uses cached items, so you have to set up a command which calculates these cached items on a

- [Setup - Delivery endpoint](/Datahub_Productsup/Setup_Delivery.md): The delivery endpoint uses cached items, so you have to set up a command which calculates these cached items on a

### Datahub_Simple_Rest

This extension adds a simple read-only rest API endpoint to Pimcore Datahub for assets and data objects. All

- [Pimcore Datahub Simple Rest API](/Datahub_Simple_Rest.md): This extension adds a simple read-only rest API endpoint to Pimcore Datahub for assets and data objects. All

#### Configuration


##### Elasticsearch

This bundle requires minimum version of Elasticsearch 8.0.

- [Elasticsearch Client Setup](/Datahub_Simple_Rest/Configuration/Elasticsearch.md): This bundle requires minimum version of Elasticsearch 8.0.

##### Endpoint_Configuration

Every Datahub configuration creates a separate endpoint with its own settings and its own data. Following

- [Endpoint Configuration Details](/Datahub_Simple_Rest/Configuration/Endpoint_Configuration.md): Every Datahub configuration creates a separate endpoint with its own settings and its own data. Following

##### Opensearch

This bundle requires minimum version of OpenSearch 2.7.

- [OpenSearch Client Setup](/Datahub_Simple_Rest/Configuration/Opensearch.md): This bundle requires minimum version of OpenSearch 2.7.

#### Filtering_and_Paging

Query Filters

- [Filtering and Paging](/Datahub_Simple_Rest/Filtering_and_Paging.md): Query Filters

#### Indexing_Details

Index Structure

- [Indexing Details](/Datahub_Simple_Rest/Indexing_Details.md): Index Structure

#### Installation

Required Bundles

- [Installation](/Datahub_Simple_Rest/Installation.md): Required Bundles

##### Upgrade_Notes

Things to consider before or when upgrading to new versions.

- [Upgrade Notes](/Datahub_Simple_Rest/Installation/Upgrade_Notes.md): Things to consider before or when upgrading to new versions.

### Datahub_Webhooks

This extension adds Webhook functionality to Pimcore Datahub for Assets, Documents, Data Objects and workflows, and provides an option for clients to subscribe to events happening within Pimcore.

- [Datahub Webhooks](/Datahub_Webhooks.md): This extension adds Webhook functionality to Pimcore Datahub for Assets, Documents, Data Objects and workflows, and provides an option for clients to subscribe to events happening within Pimcore.

#### Configuration

The General tab allows to configure the webhook basic settings such as adding a description or choosing a log level depending on your needs.

- [General Tab](/Datahub_Webhooks/Configuration.md): The General tab allows to configure the webhook basic settings such as adding a description or choosing a log level depending on your needs.

##### Events_and_Schema

In the Events & Schema tab, you can configure the events you want to subscribe to and the data (payload) sent to each of your subscribers.

- [Events & Schema Tab](/Datahub_Webhooks/Configuration/Events_and_Schema.md): In the Events & Schema tab, you can configure the events you want to subscribe to and the data (payload) sent to each of your subscribers.

##### Subscribers

In the Subscribers tab, you can add one or more subscribers to your configuration. Each subscriber will receive a request with the information set in the Events and Schema tab.

- [Subscribers Tab](/Datahub_Webhooks/Configuration/Subscribers.md): In the Subscribers tab, you can add one or more subscribers to your configuration. Each subscriber will receive a request with the information set in the Events and Schema tab.

##### Workspaces

In the Workspaces tab, you can limit the webhook configuration to include or exclude folders or folder structures.

- [Workspaces Tab](/Datahub_Webhooks/Configuration/Workspaces.md): In the Workspaces tab, you can limit the webhook configuration to include or exclude folders or folder structures.

#### Example_Requests

The structure of the request sent to the subscribers has two important sections:

- [Request structure](/Datahub_Webhooks/Example_Requests.md): The structure of the request sent to the subscribers has two important sections:

#### Extending

Extending or replacing existing processors is as easy as it gets.

- [Create a Webhook Processor](/Datahub_Webhooks/Extending.md): Extending or replacing existing processors is as easy as it gets.

#### Installation

This bundle is only supported on Pimcore Core Framework 11.

- [Installation](/Datahub_Webhooks/Installation.md): This bundle is only supported on Pimcore Core Framework 11.

##### Upgrade_notes

Update to Version 1.1

- [Update Notes](/Datahub_Webhooks/Installation/Upgrade_notes.md): Update to Version 1.1

### Datahub

Pimcore Datahub (data delivery and consumption platform) integrates different input & output channel

- [Pimcore Datahub](/Datahub.md): Pimcore Datahub (data delivery and consumption platform) integrates different input & output channel

#### Basic_Principle

Pimcore Datahub allows defining multiple endpoints that allow data delivery and consumption. These endpoints are configured via so called configurations that can be added in the Pimcore admin user interface:

- [Basic Principle](/Datahub/Basic_Principle.md): Pimcore Datahub allows defining multiple endpoints that allow data delivery and consumption. These endpoints are configured via so called configurations that can be added in the Pimcore admin user interface:

#### Deployment

The configuration by default is saved in var/config/data-hub/example.yaml.

- [Configuration & Deployment](/Datahub/Deployment.md): The configuration by default is saved in var/config/data-hub/example.yaml.

#### GraphQL

With GraphQL endpoints, Datahub allows integrating Pimcore to other systems

- [GraphQL](/Datahub/GraphQL.md): With GraphQL endpoints, Datahub allows integrating Pimcore to other systems

##### Configuration

- [Custom Permissions](/Datahub/GraphQL/Configuration/Custom_Permissions.md): When creating custom queries or creating other custom extensions to Datahub, it might be useful to add additional permissions to define access rules for certain data entities (like it is possible to define access for Documents, Assets, etc.).
- [Customizing the Endpoint](/Datahub/GraphQL/Configuration/Customize_Endpoint_URL.md): The standard endpoint is
- [General Settings](/Datahub/GraphQL/Configuration/General_Settings.md): General Settings
- [Schema Settings](/Datahub/GraphQL/Configuration/Schema_Settings.md): Schema settings define which data entities (Data Object classes, Assets, Documents) should be exposed via the endpoint. For Assets and Documents, default schemas are provided, for Data Object classes the schema can be defined in the field configuration.
- [Security Settings](/Datahub/GraphQL/Configuration/Security_Settings.md): The security settings define how the endpoint is secured and which data is accessible.

##### Events

Datahub GraphQL events are based on the Symfony event dispatcher, and are triggered during execution of Query and Mutation requests.

- [Events](/Datahub/GraphQL/Events.md): Datahub GraphQL events are based on the Symfony event dispatcher, and are triggered during execution of Query and Mutation requests.

##### Mutation

- [Add a Custom Mutation Datatype](/Datahub/GraphQL/Mutation/Add_Custom_Mutation_Datatype.md): For adding a new mutation data type two steps are necessary:
- [Add a Custom Mutation Operator](/Datahub/GraphQL/Mutation/Add_Custom_Mutation_Operator.md): For adding a new mutation operator two steps are necessary:
- [Add Custom Mutations](/Datahub/GraphQL/Mutation/Add_Custom_Mutations.md): You can extend the mutation schema and add your custom mutations in the following way.
- [Asset Mutations](/Datahub/GraphQL/Mutation/Asset_Mutations.md): Create Asset
- [DataObject Mutations](/Datahub/GraphQL/Mutation/DataObject_Mutations.md): Data object mutations are used to create, update and delete data objects, documents, assets and translations.
- [Document Mutations](/Datahub/GraphQL/Mutation/Document_Mutations.md): Supported Document Types
- [Fieldcollection Mutations](/Datahub/GraphQL/Mutation/Mutation_Samples/Fieldcollection_Mutations.md): A Field Collection
- [[FreeForm] Create Document With Areablocks and Nested Block With Images](/Datahub/GraphQL/Mutation/Mutation_Samples/FreeformAPI_Create_Document_with_Areablocks.md): See demo document 99 for reference.
- [[FreeForm] Create a New Link Document](/Datahub/GraphQL/Mutation/Mutation_Samples/FreeformAPI_Create_new_Link_Document.md): Grid
- [[FreeForm] Update Email Document](/Datahub/GraphQL/Mutation/Mutation_Samples/FreeformAPI_Update_Email_Document.md): See demo document 144 for reference.
- [Sample for Mutation Operator "Locale Collector"](/Datahub/GraphQL/Mutation/Mutation_Samples/Operators/Locale_Collector.md)
- [Sample Add Relations](/Datahub/GraphQL/Mutation/Mutation_Samples/Sample_Add_Relations.md): This will add relations to categories relation field of Car object. Type can be omitted for
- [[TreeAPI] Create Document With Areablocks and Nested Block With Images](/Datahub/GraphQL/Mutation/Mutation_Samples/TreeAPI_Create_Document_with_Areablocks.md): Note that this produces the same result as this example

##### Operators

Operators allow to modify and transform the data before it is delivered to the endpoint or stored in Pimcore,

- [Operators](/Datahub/GraphQL/Operators.md): Operators allow to modify and transform the data before it is delivered to the endpoint or stored in Pimcore,
- [If Empty](/Datahub/GraphQL/Operators/Mutation/IfEmpty.md): Only sets the value if current one is empty. Add the operator to the list and drag & drop the desired field into the operator.
- [Locale Collector](/Datahub/GraphQL/Operators/Mutation/LocalCollector.md): Allows editing all languages for a single field.
- [Locale Switcher](/Datahub/GraphQL/Operators/Mutation/LocaleSwitcher.md): localeswitcherconfig.png
- [Alias](/Datahub/GraphQL/Operators/Query/Alias.md): Simply gives the child node a different name.
- [Asset Thumbnail](/Datahub/GraphQL/Operators/Query/AssetThumbnail.md): Returns the selected thumbnail URL.
- [Asset Thumbnail HTML](/Datahub/GraphQL/Operators/Query/AssetThumbnailHTML.md): Returns the selected thumbnail HTML tag.
- [Concatenator](/Datahub/GraphQL/Operators/Query/Concatenator.md): Concatenates the values of the selected fields.
- [Date Formatter](/Datahub/GraphQL/Operators/Query/DateFormatter.md): Utilizes the PHP date formatter.
- [Element Counter](/Datahub/GraphQL/Operators/Query/ElementCounter.md): Counts the elements assigned to the selected field.
- [Static Text](/Datahub/GraphQL/Operators/Query/StaticText.md): Adds the configured static text to the query.
- [Substring](/Datahub/GraphQL/Operators/Query/Substring.md): This operator extracts a substring from a string.
- [Translate Value](/Datahub/GraphQL/Operators/Query/TranslateValue.md): Translates the values of the selected fields. For translation the default locale is used.
- [Trimmer](/Datahub/GraphQL/Operators/Query/Trimmer.md): Trims the value.

##### Query

- [Add a Custom Query](/Datahub/GraphQL/Query/Add_Custom_Query.md): You can extend the query schema and add your custom query in the following way.
- [Add a Custom Query Datatype](/Datahub/GraphQL/Query/Add_Custom_Query_Datatype.md): For adding a new query data type two steps are necessary:
- [Add a Custom Query Operator](/Datahub/GraphQL/Query/Add_Custom_Query_Operator.md): For adding a new query operator two steps are necessary:
- [Asset Queries](/Datahub/GraphQL/Query/Asset_Queries.md): Asset queries support getting single assets, single asset folders and asset listings.
- [DataObject Queries](/Datahub/GraphQL/Query/DataObject_Queries.md): Suppored Data Types
- [Document Queries](/Datahub/GraphQL/Query/Document_Queries.md): Supported Document Types
- [Filter Listings](/Datahub/GraphQL/Query/Filtering.md): You can use Pimcore's webservice filter logic
- [Localization](/Datahub/GraphQL/Query/Localization.md): Default Language
- [Classification Store](/Datahub/GraphQL/Query/Query_Samples/ClassificationStore.md): Supported Data Types
- [Advanced Many-to-Many Object Relation and Metadata](/Datahub/GraphQL/Query/Query_Samples/Sample_Advanced_ManyToMany_Object_Relation.md): Data Model for class AccessoryPart:
- [Get Advanced Many-to-Many Relation Metadata](/Datahub/GraphQL/Query/Query_Samples/Sample_Advanced_ManyToMany_Relation_Metadata.md): Metadata
- [Get Asset Metadata](/Datahub/GraphQL/Query/Query_Samples/Sample_Asset_Metadata.md): Metadata
- [Get Element Properties](/Datahub/GraphQL/Query/Query_Samples/Sample_Element_Properties.md): Sample Document Properties
- [Field-Collections on Data Objects](/Datahub/GraphQL/Query/Query_Samples/Sample_Fieldcollections.md): Configuration
- [Get Linked Data](/Datahub/GraphQL/Query/Query_Samples/Sample_Get_Linked_Data.md): Get Car With Referenced Manufacturer and Manufacturer Logo Image Thumbnail
- [Get Asset By Id](/Datahub/GraphQL/Query/Query_Samples/Sample_GetAsset.md): If you want to access an Asset directly.
- [Get Asset Listing](/Datahub/GraphQL/Query/Query_Samples/Sample_GetAssetListing.md): Request
- [Get Translation By Key](/Datahub/GraphQL/Query/Query_Samples/Sample_GetTranslation.md): If you want to access one translation.
- [Get Translation Listing](/Datahub/GraphQL/Query/Query_Samples/Sample_GetTranslationListing.md): Request
- [Get Manufacturer Listing](/Datahub/GraphQL/Query/Query_Samples/Sample_Manufacturer_Listing.md): Grid
- [Many-to-Many Object Relation](/Datahub/GraphQL/Query/Query_Samples/Sample_ManyToMany_Object_Relation.md): Data
- [Get objects Parent/Children/Siblings](/Datahub/GraphQL/Query/Query_Samples/Sample_Parent_Children_Siblings.md): For example, to get berlina object's (id:261) parent, children and siblings
- [Translate Values](/Datahub/GraphQL/Query/Query_Samples/Sample_Translate_Values.md): The following example translates the AccessoryPart condition value.
- [Using Aliases](/Datahub/GraphQL/Query/Using_Aliases.md): Aliases are used to rename the result of a field.

#### Installation_and_Upgrade

Bundle Installation

- [Installation](/Datahub/Installation_and_Upgrade.md): Bundle Installation

##### Upgrade_Notes

2.2.0

- [Upgrade Notes](/Datahub/Installation_and_Upgrade/Upgrade_Notes.md): 2.2.0

#### Testing

Perform PHPStan Analysis

- [Testing](/Datahub/Testing.md): Perform PHPStan Analysis

### Direct_Edit

Pimcore Direct Edit allows you editing Pimcore Assets locally in your preferred editor and upload them right away back to Pimcore.

- [Pimcore Direct Edit](/Direct_Edit.md): Pimcore Direct Edit allows you editing Pimcore Assets locally in your preferred editor and upload them right away back to Pimcore.

#### Installation

Bundle Installation

- [Installation & Configuration](/Direct_Edit/Installation.md): Bundle Installation

##### Mercure_Setup

Start and Configure Mercure

- [Mercure Setup](/Direct_Edit/Installation/Mercure_Setup.md): Start and Configure Mercure

##### Upgrade_notes

Update to Version 2.5

- [Update Notes](/Direct_Edit/Installation/Upgrade_notes.md): Update to Version 2.5

#### Integrate_into_Custom_Application

1 Create a custom Permission Service

- [Integrate Direct Edit Button into Custom Application](/Direct_Edit/Integrate_into_Custom_Application.md): 1 Create a custom Permission Service

#### Pimcore_Direct_Edit_Client

Pimcore Direct Edit Client is a desktop client that is necessary to allow local file edits.

- [Pimcore Direct Edit Client](/Direct_Edit/Pimcore_Direct_Edit_Client.md): Pimcore Direct Edit Client is a desktop client that is necessary to allow local file edits.

### Ecommerce_Framework

Why Pimcore E-Commerce Framework

- [E-Commerce Framework](/Ecommerce_Framework.md): Why Pimcore E-Commerce Framework

#### Architecture_Overview

The following architecture overview shows available framework components. The component configuration takes place in

- [Architecture Overview](/Ecommerce_Framework/Architecture_Overview.md): The following architecture overview shows available framework components. The component configuration takes place in

#### Cart_Manager

The Cart Manager is responsible for all aspects concerning carts and can manage multiple carts.

- [Cart Manager](/Ecommerce_Framework/Cart_Manager.md): The Cart Manager is responsible for all aspects concerning carts and can manage multiple carts.

#### Checkout_Manager

The Checkout Manager is responsible for all aspects concerning checkout process and an one-stop API for getting

- [Checkout Manager](/Ecommerce_Framework/Checkout_Manager.md): The Checkout Manager is responsible for all aspects concerning checkout process and an one-stop API for getting

##### Basic_Configuration

The configuration takes place in the pimcoreecommerceframework.checkoutmanager configuration section and is tenant aware.

- [Basic Configuration](/Ecommerce_Framework/Checkout_Manager/Basic_Configuration.md): The configuration takes place in the pimcoreecommerceframework.checkoutmanager configuration section and is tenant aware.

##### Checkout_Manager_Details

Following documentation page provide a few more insights on Checkout Manager Architecture.

- [Checkout Manager Details](/Ecommerce_Framework/Checkout_Manager/Checkout_Manager_Details.md): Following documentation page provide a few more insights on Checkout Manager Architecture.

##### Checkout_Steps

For each checkout step (e.g. delivery address, delivery date, ...) there has to be a concrete checkout step implementation.

- [Checkout Steps](/Ecommerce_Framework/Checkout_Manager/Checkout_Steps.md): For each checkout step (e.g. delivery address, delivery date, ...) there has to be a concrete checkout step implementation.

##### Checkout_Tenants

The E-Commerce Framework has the concept of Checkout Tenants which allow different cart manager and checkout manager

- [Checkout Tenants for Checkout](/Ecommerce_Framework/Checkout_Manager/Checkout_Tenants.md): The E-Commerce Framework has the concept of Checkout Tenants which allow different cart manager and checkout manager

##### Committing_Orders

After all checkout steps are completed, the order can be committed. If no payment is involved, this is done as follows.

- [Committing Orders](/Ecommerce_Framework/Checkout_Manager/Committing_Orders.md): After all checkout steps are completed, the order can be committed. If no payment is involved, this is done as follows.

##### Integrating_Payment

To integrate payment into the checkout process, instead of calling $manager->commitOrder(); like described

- [Payment Integration](/Ecommerce_Framework/Checkout_Manager/Integrating_Payment.md): To integrate payment into the checkout process, instead of calling $manager->commitOrder(); like described

#### Configuration

The E-Commerce Framework is implemented as semantic bundle configuration which means that you can configure the framework

- [Configuration](/Ecommerce_Framework/Configuration.md): The E-Commerce Framework is implemented as semantic bundle configuration which means that you can configure the framework

##### PimcoreEcommerceFrameworkBundle_Configuration_Reference

The following is the generated reference for the pimcoreecommerceframework configuration tree. This reference can

- [PimcoreEcommerceFrameworkBundle Configuration Reference](/Ecommerce_Framework/Configuration/PimcoreEcommerceFrameworkBundle_Configuration_Reference.md): The following is the generated reference for the pimcoreecommerceframework configuration tree. This reference can

#### Event_API_and_Event_Manager

General

- [Events and Event Listeners](/Ecommerce_Framework/Event_API_and_Event_Manager.md): General

#### Filter_Service

Basic Idea of the Filter Service

- [Filter Service](/Ecommerce_Framework/Filter_Service.md): Basic Idea of the Filter Service

##### Elastic_Search

Definition of Filter Types

- [Filter Service with Elasticsearch](/Ecommerce_Framework/Filter_Service/Elastic_Search.md): Definition of Filter Types
- [Filter Classification Store](/Ecommerce_Framework/Filter_Service/Elastic_Search/Filter_Classification_Store.md): With elasticsearch it is possible to index all attributes of Classification Store
- [Filter for nested documents](/Ecommerce_Framework/Filter_Service/Elastic_Search/Filter_Nested_Documents.md): In some cases it is necessary to store an array of objects, but in a way so that they can be queried independently of each

##### Open_Search

Definition of Filter Types

- [Filter Service with OpenSearch](/Ecommerce_Framework/Filter_Service/Open_Search.md): Definition of Filter Types
- [Filter Classification Store](/Ecommerce_Framework/Filter_Service/Open_Search/Filter_Classification_Store.md): With OpenSearch it is possible to index all attributes of Classification Store
- [Filter for nested documents](/Ecommerce_Framework/Filter_Service/Open_Search/Filter_Nested_Documents.md): In some cases it is necessary to store an array of objects, but in a way so that they can be queried independently of each

#### Index_Service

The Index Service (in combination with the Filter Service) provides functionality for indexing, listing, filtering and

- [Index Service](/Ecommerce_Framework/Index_Service.md): The Index Service (in combination with the Filter Service) provides functionality for indexing, listing, filtering and

##### Mockup_Objects

Normally the result of Product Lists contain Pimcore product objects. When retrieving lists with many entries this can

- [Mockup Objects in Product List Results](/Ecommerce_Framework/Index_Service/Mockup_Objects.md): Normally the result of Product Lists contain Pimcore product objects. When retrieving lists with many entries this can

##### Product_Index_Configuration

The configuration of the Product Index defines the content of the Product Index (which attributes are extracted how

- [Product Index Configuration](/Ecommerce_Framework/Index_Service/Product_Index_Configuration.md): The configuration of the Product Index defines the content of the Product Index (which attributes are extracted how
- [Assortment Tenant Configuration](/Ecommerce_Framework/Index_Service/Product_Index_Configuration/Assortment_Tenant_Configuration.md): The E-Commerce Framework provides a two level Assortment Tenant system for the Product Index:
- [Data Architecture and Indexing Process](/Ecommerce_Framework/Index_Service/Product_Index_Configuration/Data_Architecture_and_Indexing_Process.md): Depending on the Product Index implementation, there are two different Product Index data architectures and ways for
- [Special Aspects for Elasticsearch](/Ecommerce_Framework/Index_Service/Product_Index_Configuration/Elastic_Search.md): Basically Elasticsearch worker works as described in the optimized architecture.
- [Configuration Configuration](/Ecommerce_Framework/Index_Service/Product_Index_Configuration/Elastic_Search/Configuration_Details.md): Following aspects need to be considered in index configuration:
- [Synonyms](/Ecommerce_Framework/Index_Service/Product_Index_Configuration/Elastic_Search/Synonyms.md): Synonyms
- [Special Aspects for Findologic Exporter](/Ecommerce_Framework/Index_Service/Product_Index_Configuration/Findologic.md): Basically findologic worker works as described in the optimized architecture. But there is an additional
- [Special Aspects for OpenSearch](/Ecommerce_Framework/Index_Service/Product_Index_Configuration/Open_Search.md): Basically OpenSearch worker works as described in the optimized architecture.
- [Configuration Configuration](/Ecommerce_Framework/Index_Service/Product_Index_Configuration/Open_Search/Configuration_Details.md): Following aspects need to be considered in index configuration:
- [Synonyms](/Ecommerce_Framework/Index_Service/Product_Index_Configuration/Open_Search/Synonyms.md): Synonyms

##### Product_List

Working with Product Lists

- [Product List](/Ecommerce_Framework/Index_Service/Product_List.md): Working with Product Lists

#### Installation

This section describes the installation of the E-Commerce Framework and the first steps of configuration.

- [Installation and First Configuration](/Ecommerce_Framework/Installation.md): This section describes the installation of the E-Commerce Framework and the first steps of configuration.

##### Upgrade_Notes

v2.1.0

- [Upgrade Notes](/Ecommerce_Framework/Installation/Upgrade_Notes.md): v2.1.0

#### Order_Manager

The Order Manager is responsible for all aspects of working with orders except committing them (which is the

- [Order Manager](/Ecommerce_Framework/Order_Manager.md): The Order Manager is responsible for all aspects of working with orders except committing them (which is the

##### Working_with_Order_Agent

The Order Agent is a one stop API for working with orders, e.g. changing state of orders, modifying quantity of items, etc.

- [Working with Order Agent](/Ecommerce_Framework/Order_Manager/Working_with_Order_Agent.md): The Order Agent is a one stop API for working with orders, e.g. changing state of orders, modifying quantity of items, etc.

##### Working_with_Order_Lists

The Order List are a one stop API for filtering and listing order objects. Of course default Pimcore object lists also

- [Working with Order Lists](/Ecommerce_Framework/Order_Manager/Working_with_Order_Lists.md): The Order List are a one stop API for filtering and listing order objects. Of course default Pimcore object lists also

#### Payment

The Payment Manager is responsible for all aspects concerning payment. The main aspect is the implementation

- [Payment Manager](/Ecommerce_Framework/Payment.md): The Payment Manager is responsible for all aspects concerning payment. The main aspect is the implementation

##### Recurring_Payments

Pimcore currently supports recurring payment for the payment provider Datatrans (Alias).

- [Recurring Payment](/Ecommerce_Framework/Payment/Recurring_Payments.md): Pimcore currently supports recurring payment for the payment provider Datatrans (Alias).

#### Tracking_Manager

The Tracking Manager enables e-commerce transaction tracking for e-commerce websites built with the framework. Due to

- [Tracking Manager](/Ecommerce_Framework/Tracking_Manager.md): The Tracking Manager enables e-commerce transaction tracking for e-commerce websites built with the framework. Due to

#### Upgrade_Notes

Version 2.0.0

- [Upgrade Notes](/Ecommerce_Framework/Upgrade_Notes.md): Version 2.0.0

#### Working_with_Availabilities

For availabilities there is a similar concepts like the PriceSystems

- [Working with Availabilities](/Ecommerce_Framework/Working_with_Availabilities.md): For availabilities there is a similar concepts like the PriceSystems

#### Working_with_Prices

Prices are an essential part of every E-Commerce solution. In order to be able to implement complex and very custom

- [Working with Prices](/Ecommerce_Framework/Working_with_Prices.md): Prices are an essential part of every E-Commerce solution. In order to be able to implement complex and very custom

##### Calculate_with_Prices

As floating point numbers (float, double) are not able to represent numbers exactly (see here

- [Calculate with Prices](/Ecommerce_Framework/Working_with_Prices/Calculate_with_Prices.md): As floating point numbers (float, double) are not able to represent numbers exactly (see here

##### Price_Systems_and_getting_Prices

In terms of pricing, the E-Commerce Framework has the concept of Price Systems. These Price Systems are responsible for

- [Price Systems](/Ecommerce_Framework/Working_with_Prices/Price_Systems_and_getting_Prices.md): In terms of pricing, the E-Commerce Framework has the concept of Price Systems. These Price Systems are responsible for

##### Pricing_Rules

Pricing Rules are supported by the E-Commerce Framework out of the box. The pricing rules themselves can be configured

- [Pricing Rules](/Ecommerce_Framework/Working_with_Prices/Pricing_Rules.md): Pricing Rules are supported by the E-Commerce Framework out of the box. The pricing rules themselves can be configured

##### Tax_Management

Within the Price Systems there is a Tax Management component to deal with all sorts of taxes.

- [Tax Management](/Ecommerce_Framework/Working_with_Prices/Tax_Management.md): Within the Price Systems there is a Tax Management component to deal with all sorts of taxes.

##### Vouchers

Like Pricing Rules, also vouchers are supported out of the box by the framework.

- [Vouchers](/Ecommerce_Framework/Working_with_Prices/Vouchers.md): Like Pricing Rules, also vouchers are supported out of the box by the framework.

### Enterprise_Metadata

This extension adds Enterprise Asset Metadata. It allows configuring Asset metadata similarly to Data Object class definitions and adds additional data types for Asset metadata to Pimcore.

- [Pimcore Enterprise Asset Metadata](/Enterprise_Metadata.md): This extension adds Enterprise Asset Metadata. It allows configuring Asset metadata similarly to Data Object class definitions and adds additional data types for Asset metadata to Pimcore.

#### Installation

Minimum Requirements

- [Installation](/Enterprise_Metadata/Installation.md): Minimum Requirements

##### Update

Following steps are necessary during updating to newer versions.

- [Upgrade Information](/Enterprise_Metadata/Installation/Update.md): Following steps are necessary during updating to newer versions.

#### Technical_Details

Migration of existing Data

- [Technical Details](/Enterprise_Metadata/Technical_Details.md): Migration of existing Data

### Generic_Data_Index

The Pimcore Generic Data Index Bundle provides a centralized way to index and search elements (assets, data objects and documents) in Pimcore via indices (e.g OpenSearch, Elasticsearch).

- [Pimcore Generic Data Index](/Generic_Data_Index.md): The Pimcore Generic Data Index Bundle provides a centralized way to index and search elements (assets, data objects and documents) in Pimcore via indices (e.g OpenSearch, Elasticsearch).

#### Configuration

The Generic Data Index Bundle provides a generic way to index and search data in Pimcore. It is shipped with the OpenSearch and Elasticsearch clients and provides a central configuration for it in order to be used in other bundles.

- [Configuration of the Generic Data Index Bundle](/Generic_Data_Index/Configuration.md): The Generic Data Index Bundle provides a generic way to index and search data in Pimcore. It is shipped with the OpenSearch and Elasticsearch clients and provides a central configuration for it in order to be used in other bundles.

##### Elasticsearch

This bundle requires minimum version of Elasticsearch 8.0.

- [Elasticsearch Client Setup](/Generic_Data_Index/Configuration/Elasticsearch.md): This bundle requires minimum version of Elasticsearch 8.0.

##### Index_Management

It is important to index all assets and data object in Pimcore in order to be able to use the search and listing features powered by the Generic Data Index bundle.

- [Index Management](/Generic_Data_Index/Configuration/Index_Management.md): It is important to index all assets and data object in Pimcore in order to be able to use the search and listing features powered by the Generic Data Index bundle.

##### Opensearch

Supported versions of OpenSearch are 2.7. to 2.19

- [OpenSearch Client Setup](/Generic_Data_Index/Configuration/Opensearch.md): Supported versions of OpenSearch are 2.7. to 2.19

#### Extending_Data_Index

Generic Data Index bundle provides possibility to use data indices to handle search, listings, and filters. Consequently, it's crucial to store data from Pimcore elements into data indices.

- [Extending Data Index](/Generic_Data_Index/Extending_Data_Index.md): Generic Data Index bundle provides possibility to use data indices to handle search, listings, and filters. Consequently, it's crucial to store data from Pimcore elements into data indices.

##### Extend_Search_Index

Extending Search Index via Events

- [Extending Search Index](/Generic_Data_Index/Extending_Data_Index/Extend_Search_Index.md): Extending Search Index via Events

#### Installation

This bundle requires minimum version of OpenSearch 2.7. or Elasticsearch 8.0.0.

- [Installation of Generic Data Index](/Generic_Data_Index/Installation.md): This bundle requires minimum version of OpenSearch 2.7. or Elasticsearch 8.0.0.

##### Upgrade

Following steps are necessary during updating to newer versions.

- [Upgrade Information](/Generic_Data_Index/Installation/Upgrade.md): Following steps are necessary during updating to newer versions.

#### Searching_For_Data_In_Index

The Generic Data Index bundle adds standardized and flexible services to search data from the search indices.

- [Searching For Data In Index](/Generic_Data_Index/Searching_For_Data_In_Index.md): The Generic Data Index bundle adds standardized and flexible services to search data from the search indices.

##### Default_Search_Models

All models under namespace Pimcore\Bundle\GenericDataIndexBundle\Model\OpenSearch are deprecated and will be removed in version 2.0

- [Default Search Models](/Generic_Data_Index/Searching_For_Data_In_Index/Default_Search_Models.md): All models under namespace Pimcore\Bundle\GenericDataIndexBundle\Model\OpenSearch are deprecated and will be removed in version 2.0

##### Permissions_Workspaces

The Generic Data Index bundle respects the user permissions and user workspaces in connection to his roles.

- [Permissions and Workspaces](/Generic_Data_Index/Searching_For_Data_In_Index/Permissions_Workspaces.md): The Generic Data Index bundle respects the user permissions and user workspaces in connection to his roles.

##### Pimcore_Query_Language

Pimcore Query Language (PQL) is a query language that allows you to search for data in the Pimcore Generic Data Index. It is a simple and powerful query language that allows you to search for data using a wide range of search criteria.

- [Pimcore Query Language](/Generic_Data_Index/Searching_For_Data_In_Index/Pimcore_Query_Language.md): Pimcore Query Language (PQL) is a query language that allows you to search for data in the Pimcore Generic Data Index. It is a simple and powerful query language that allows you to search for data using a wide range of search criteria.
- [Use Pimcore Query Language (PQL) as a Developer](/Generic_Data_Index/Searching_For_Data_In_Index/Pimcore_Query_Language/Use_PQL_as_Developer.md): Execute searches based on PQL queries

##### Search_Modifiers

Search modifiers can influence the search results by modifying the search query. They can be used to filter, sort or aggregate the search results.

- [Search Modifiers](/Generic_Data_Index/Searching_For_Data_In_Index/Search_Modifiers.md): Search modifiers can influence the search results by modifying the search query. They can be used to filter, sort or aggregate the search results.

### Google_Marketing

The Marketing Settings gives you the possibility to configure marketing-specific settings, which are:

- [Google Marketing Bundle](/Google_Marketing.md): The Marketing Settings gives you the possibility to configure marketing-specific settings, which are:

#### Analytics

In order to use Google Analytics you have to install the PimcoreGoogleMarketingBundle.

- [Google Analytics](/Google_Marketing/Analytics.md): In order to use Google Analytics you have to install the PimcoreGoogleMarketingBundle.

#### Google_Services_Integration

Menus and buttons may vary depending on the current GUI version

- [Google Services Integration](/Google_Marketing/Google_Services_Integration.md): Menus and buttons may vary depending on the current GUI version

### Headless_Documents

The Headless Documents extension enables managing website content in a headless manner and makes it accessible for

- [Pimcore Headless Documents](/Headless_Documents.md): The Headless Documents extension enables managing website content in a headless manner and makes it accessible for

#### Configuration

By default, headless template and brick configurations are stored in Symfony config yaml files

- [Configuration](/Headless_Documents/Configuration.md): By default, headless template and brick configurations are stored in Symfony config yaml files

#### DataHub_Integration

The Datahub Integration is a feature that allows you to connect your Headless Documents to the Pimcore Datahub.

- [Datahub Integration](/Headless_Documents/DataHub_Integration.md): The Datahub Integration is a feature that allows you to connect your Headless Documents to the Pimcore Datahub.

##### Configuration

General Tab

- [Configuration](/Headless_Documents/DataHub_Integration/Configuration.md): General Tab

##### Search_Endpoint

The search endpoint allows you to query Headless Documents via a REST endpoint.

- [Search Endpoint](/Headless_Documents/DataHub_Integration/Search_Endpoint.md): The search endpoint allows you to query Headless Documents via a REST endpoint.

#### Headless_Documents

A Headless Document represents a typical web-page - very similar to standard Pimcore documents.

- [Headless Documents](/Headless_Documents/Headless_Documents.md): A Headless Document represents a typical web-page - very similar to standard Pimcore documents.

#### Installation

Minimum Requirements

- [Installation](/Headless_Documents/Installation.md): Minimum Requirements

##### Upgrade

Update to Version 2.3

- [Update Notes](/Headless_Documents/Installation/Upgrade.md): Update to Version 2.3

#### Template_Configuration

The content structure of a Headless Document is defined by the template configuration, which defines the layout and the

- [Headless Template Configuration](/Headless_Documents/Template_Configuration.md): The content structure of a Headless Document is defined by the template configuration, which defines the layout and the

##### Headless_Bricks

Headless Bricks allow the configuration of a reusable set of editables that can be used in Headless Documents with the

- [Headless Bricks](/Headless_Documents/Template_Configuration/Headless_Bricks.md): Headless Bricks allow the configuration of a reusable set of editables that can be used in Headless Documents with the

##### Layouts

A Layout is a vital part of headless template configuration, which defines the structure for Pimcore editables

- [Layouts](/Headless_Documents/Template_Configuration/Layouts.md): A Layout is a vital part of headless template configuration, which defines the structure for Pimcore editables
- [Adding a Custom Layout](/Headless_Documents/Template_Configuration/Layouts/Adding_Custom_Layout.md): For adding new a layout type, follow these steps:

### Light_Theme_Admin_UI

This enterprise extension includes the following enhancements for Admin UI Classic:

- [Light Theme for Admin UI Classic](/Light_Theme_Admin_UI.md): This enterprise extension includes the following enhancements for Admin UI Classic:

#### Installation

This bundle is only supported on Pimcore Core Framework 11.

- [Installation](/Light_Theme_Admin_UI/Installation.md): This bundle is only supported on Pimcore Core Framework 11.

### Newsletter

This bundle provides a basic newsletter framework with the advantage to use all the data that is already stored in the system.

- [Pimcore Newsletter Bundle](/Newsletter.md): This bundle provides a basic newsletter framework with the advantage to use all the data that is already stored in the system.

#### Installation

Minimum Requirements

- [Installation](/Newsletter/Installation.md): Minimum Requirements

#### Newsletter_Config

General

- [Newsletter Configuration](/Newsletter/Newsletter_Config.md): General

#### Newsletter_Documents

Newsletter documents are the way to create and send newsletters directly within Pimcore.

- [Newsletter Document](/Newsletter/Newsletter_Documents.md): Newsletter documents are the way to create and send newsletters directly within Pimcore.

### OpenID_Connect

Pimcore OpenID Connect allows to configure SSO for Pimcore backend login with

- [Pimcore OpenID Connect](/OpenID_Connect.md): Pimcore OpenID Connect allows to configure SSO for Pimcore backend login with

#### Configuration

Configuration takes places in configuration files or can be applied directly in Pimcore

- [Configuration](/OpenID_Connect/Configuration.md): Configuration takes places in configuration files or can be applied directly in Pimcore

#### Installation

Bundle Installation

- [Installation](/OpenID_Connect/Installation.md): Bundle Installation

#### Technical_Detail_Aspects

Following aspects might be important to know in order to better understand how the integration works.

- [Technical Detail Aspects](/OpenID_Connect/Technical_Detail_Aspects.md): Following aspects might be important to know in order to better understand how the integration works.

#### Upgrade_Notes

1.2.0

- [Upgrade Notes](/OpenID_Connect/Upgrade_Notes.md): 1.2.0

### Paas

What is Pimcore PaaS and how does it work?

- [Pimcore PaaS](/Paas.md): What is Pimcore PaaS and how does it work?

#### Common_Pitfalls

This guide covers the most common issues teams encounter when deploying Pimcore PaaS and how to prevent them.

- [Common Pitfalls and How to Avoid Them](/Paas/Common_Pitfalls.md): This guide covers the most common issues teams encounter when deploying Pimcore PaaS and how to prevent them.

#### Configuration

The template configuration files, which were installed by the previous step, need to be customized for your project.

- [Customize Infrastructure Configuration](/Paas/Configuration.md): The template configuration files, which were installed by the previous step, need to be customized for your project.

#### Faq

Nginx/Router

- [FAQ](/Paas/Faq.md): Nginx/Router

#### Good_to_know

Sync existing data

- [Good to know](/Paas/Good_to_know.md): Sync existing data

#### Migration

Important notes:

- [Migrating Pimcore from on-premise to Pimcore PaaS](/Paas/Migration.md): Important notes:

#### Performance_issues_while_manipulating_images

Pimcore generates thumbnails on the fly when they are not created yet, see https://docs.pimcore.com/platform/Pimcore/Assets/WorkingwithThumbnails/Image_Thumbnails/#dynamic-generation-on-request.

- [Performance issues while manipulating images](/Paas/Performance_issues_while_manipulating_images.md): Pimcore generates thumbnails on the fly when they are not created yet, see https://docs.pimcore.com/platform/Pimcore/Assets/WorkingwithThumbnails/Image_Thumbnails/#dynamic-generation-on-request.

### Perspective_Editor

This bundle provides an editor for Pimcore to manage custom views and perspectives.

- [Pimcore Perspective Editor](/Perspective_Editor.md): This bundle provides an editor for Pimcore to manage custom views and perspectives.

#### Customize_Menu_Entry_List

Custom view and perspective settings also allow configuring the context menu and toolbar menu entries.

- [Customize Menu Entry List](/Perspective_Editor/Customize_Menu_Entry_List.md): Custom view and perspective settings also allow configuring the context menu and toolbar menu entries.

#### Installation

Bundle Installation

- [Installation](/Perspective_Editor/Installation.md): Bundle Installation

#### Upgrade

Update to Version 1.8

- [Update Notes](/Perspective_Editor/Upgrade.md): Update to Version 1.8

### Pimcore

This documentation section provides all information you need to use the Core Framework of Pimcore.

- [Pimcore Core Framework Documentation](/Pimcore.md): This documentation section provides all information you need to use the Core Framework of Pimcore.

#### Administration_of_Pimcore

This chapter is a collection of topics regarding the administration of Pimcore for developers. The topics mentioned here

- [Administration of Pimcore](/Pimcore/Administration_of_Pimcore.md): This chapter is a collection of topics regarding the administration of Pimcore for developers. The topics mentioned here

##### Backups

We recommend the usage of standard tools depending on your infrastructure for creating a backup of your Pimcore instance.

- [Backup of Pimcore](/Pimcore/Administration_of_Pimcore/Backups.md): We recommend the usage of standard tools depending on your infrastructure for creating a backup of your Pimcore instance.

##### Cleanup_Data_Storage

In general Pimcore is quite maintenance-free in terms of cleaning up the filesystem from temporary files, log files,

- [Cleanup Data Storage](/Pimcore/Administration_of_Pimcore/Cleanup_Data_Storage.md): In general Pimcore is quite maintenance-free in terms of cleaning up the filesystem from temporary files, log files,

##### Commandline_Interface

Pimcore offers certain tasks a commandline command.

- [Commandline Interface](/Pimcore/Administration_of_Pimcore/Commandline_Interface.md): Pimcore offers certain tasks a commandline command.

##### Users_and_Roles

General

- [Users and Roles](/Pimcore/Administration_of_Pimcore/Users_and_Roles.md): General

#### Assets

Assets are files that can be managed within the Pimcore system which you can organize in folders. The most common assets

- [Assets](/Pimcore/Assets.md): Assets are files that can be managed within the Pimcore system which you can organize in folders. The most common assets

##### Accessing_Assets_via_WebDAV

Pimcore provides the option to access all assets via WebDAV. To do so,

- [Accessing Pimcore Assets via WebDAV](/Pimcore/Assets/Accessing_Assets_via_WebDAV.md): Pimcore provides the option to access all assets via WebDAV. To do so,

##### Restricting_Public_Asset_Access

Pimcore has following default behavior in terms of asset delivery:

- [Restricting Public Asset Access](/Pimcore/Assets/Restricting_Public_Asset_Access.md): Pimcore has following default behavior in terms of asset delivery:

##### Working_with_PHP_API

Pimcore provides an object orientated PHP API to work with Assets.

- [Working With Assets via PHP API](/Pimcore/Assets/Working_with_PHP_API.md): Pimcore provides an object orientated PHP API to work with Assets.

##### Working_with_Thumbnails

Pimcore provides a sophisticated thumbnail processing engine for calculating thumbnails based on source assets. So for

- [Working With Thumbnails](/Pimcore/Assets/Working_with_Thumbnails.md): Pimcore provides a sophisticated thumbnail processing engine for calculating thumbnails based on source assets. So for
- [Asset Document Thumbnails (PDF, DOCX, ODF, ...)](/Pimcore/Assets/Working_with_Thumbnails/Document_Thumbnails.md): This feature allows you to create an image thumbnail of nearly any document format, like doc(x), ppt(x), pdf, xls(x),
- [Image Thumbnails](/Pimcore/Assets/Working_with_Thumbnails/Image_Thumbnails.md): For images, Pimcore offers an advanced thumbnail-service also called 'image-pipeline'. It allows you to transform images
- [Video Thumbnails](/Pimcore/Assets/Working_with_Thumbnails/Video_Thumbnails.md): Pimcore is able to convert any video to web formats automatically. It is also possible capture a

#### Best_Practice

Please note that due to the updating of this part of the documentation, some information is only valid for previous versions of Pimcore.

- [Best Practice](/Pimcore/Best_Practice.md): Please note that due to the updating of this part of the documentation, some information is only valid for previous versions of Pimcore.

##### Adding_Button_To_Object_Editor

Sometimes it might be useful to add additional buttons to the object editor (or any other editor) in Pimcore Backend

- [Add a Button to Object Editor](/Pimcore/Best_Practice/Adding_Button_To_Object_Editor.md): Sometimes it might be useful to add additional buttons to the object editor (or any other editor) in Pimcore Backend

##### Build_Role_Rights_System_for_Frontends

A common use case for Pimcore applications are portals with user logins and a complex permission structure like for

- [Build Role & Rights System for Frontends](/Pimcore/Best_Practice/Build_Role_Rights_System_for_Frontends.md): A common use case for Pimcore applications are portals with user logins and a complex permission structure like for

##### Building_Custom_Rest_APIs

Pimcore offers a bundle called Datahub, offering a highly configurable GraphQL interface on most Pimcore entities.

- [How to Build a Custom REST API Endpoint](/Pimcore/Best_Practice/Building_Custom_Rest_APIs.md): Pimcore offers a bundle called Datahub, offering a highly configurable GraphQL interface on most Pimcore entities.

##### Implementing_Product_Information_Management

The concept of PIM encompasses a set of technologies and procedures that allow centralized management of product data and their distribution across different channels. In many common scenarios, information relating to products may come from multiple sources, potentially with different data structures, both due to different needs dictated by their distribution channels and through the provision of different technologies in distinct areas within the same company.

- [Implementing Product Information Management](/Pimcore/Best_Practice/Implementing_Product_Information_Management.md): The concept of PIM encompasses a set of technologies and procedures that allow centralized management of product data and their distribution across different channels. In many common scenarios, information relating to products may come from multiple sources, potentially with different data structures, both due to different needs dictated by their distribution channels and through the provision of different technologies in distinct areas within the same company.

##### Integrating_Commerce_Data_with_Content

Content commerce, shop everywhere, vanish separation of content and commerce - these are key phrases that popup with

- [Integrating Commerce Data With Content](/Pimcore/Best_Practice/Integrating_Commerce_Data_with_Content.md): Content commerce, shop everywhere, vanish separation of content and commerce - these are key phrases that popup with

##### Modifying_Permissions_based_on_Object_Data

The event OBJECTGETPRESENDDATA

- [Modifying Permissions Based on Object Data](/Pimcore/Best_Practice/Modifying_Permissions_based_on_Object_Data.md): The event OBJECTGETPRESENDDATA

##### Multilanguage_Setup

Best practice for multi-language websites

- [Multilanguage i18n Websites](/Pimcore/Best_Practice/Multilanguage_Setup.md): Best practice for multi-language websites

##### Object_Bricks_vs_Classification_Store

Pimcore offers many possibilities regarding data modeling for structured data (= Pimcore Data Objects).

- [Object Bricks vs Classification Store](/Pimcore/Best_Practice/Object_Bricks_vs_Classification_Store.md): Pimcore offers many possibilities regarding data modeling for structured data (= Pimcore Data Objects).

##### Object_Data_Inheritance

Object Data Inheritance is a powerful feature to minimize the data maintenance effort for editors.

- [Object Data Inheritance in Action](/Pimcore/Best_Practice/Object_Data_Inheritance.md): Object Data Inheritance is a powerful feature to minimize the data maintenance effort for editors.

##### Open_By-External_Id

There are 2 different approaches:

- [Open By External Id](/Pimcore/Best_Practice/Open_By-External_Id.md): There are 2 different approaches:

##### Primary-Replica_Database_Connection

IMPORTANT: Please be aware that the primary/replica connection can only be used for a clustered MariaDB/MySQL environment, NOT

- [How to Configure Pimcore To Use a Primary/Replica Database Connection](/Pimcore/Best_Practice/Primary-Replica_Database_Connection.md): IMPORTANT: Please be aware that the primary/replica connection can only be used for a clustered MariaDB/MySQL environment, NOT

##### Security_Concept

We at Pimcore take security very seriously and recommend a multi-layer security concept to keep Pimcore-based solutions

- [Security Concept](/Pimcore/Best_Practice/Security_Concept.md): We at Pimcore take security very seriously and recommend a multi-layer security concept to keep Pimcore-based solutions

##### Showing_Custom_Layouts_based_on_Object_Data

The event OBJECTGETPRESENDDATA

- [Showing Custom Layouts Based on Object Data](/Pimcore/Best_Practice/Showing_Custom_Layouts_based_on_Object_Data.md): The event OBJECTGETPRESENDDATA

##### Style_Backend_Depending_On_App-Env

Sometimes it makes sense to style the Pimcore backend UI depending on the application environment (APP_ENV) so that

- [Style Backend Depending on the Application Environment](/Pimcore/Best_Practice/Style_Backend_Depending_On_App-Env.md): Sometimes it makes sense to style the Pimcore backend UI depending on the application environment (APP_ENV) so that

##### Using_Tags_for_Filtering

The Pimcore Tags functionality is primarily designed

- [Using Pimcore Tags for Filtering in Frontend](/Pimcore/Best_Practice/Using_Tags_for_Filtering.md): The Pimcore Tags functionality is primarily designed

##### Where_To_Store_Sessions

Pimcore uses the Symfony session component to handle sessions. By default, Symfony stores sessions in the filesystem.

- [Where To Store Sessions](/Pimcore/Best_Practice/Where_To_Store_Sessions.md): Pimcore uses the Symfony session component to handle sessions. By default, Symfony stores sessions in the filesystem.

##### Working_With_Runtime_Cache

Pimcore heavily uses runtime cache to cache API results for performance reasons. However, it is very crucial to understand that how to deal with cached results so that correct data should utilized from the API. Let's take few examples to understand similar situations:

- [Working With Runtime Cache](/Pimcore/Best_Practice/Working_With_Runtime_Cache.md): Pimcore heavily uses runtime cache to cache API results for performance reasons. However, it is very crucial to understand that how to deal with cached results so that correct data should utilized from the API. Let's take few examples to understand similar situations:

#### Deployment

General

- [Deployment Recommendations](/Pimcore/Deployment.md): General

##### Configuration_Environments

Environment-Specific Configurations

- [Configuration](/Pimcore/Deployment/Configuration_Environments.md): Environment-Specific Configurations

##### Deployment_Tools

Following tools are provided by Pimcore to support deployment processes.

- [Deployment Tools](/Pimcore/Deployment/Deployment_Tools.md): Following tools are provided by Pimcore to support deployment processes.

##### Version_Control_Systems

Since Pimcore creates lots of temporary files during runtime it's recommended to exclude certain paths from your VCS.

- [Version Control Systems](/Pimcore/Deployment/Version_Control_Systems.md): Since Pimcore creates lots of temporary files during runtime it's recommended to exclude certain paths from your VCS.

#### Development_Tools_and_Details

This section of documentation gives insights into development tools shipped with Pimcore (e.g. features

- [Development Tools and Details](/Pimcore/Development_Tools_and_Details.md): This section of documentation gives insights into development tools shipped with Pimcore (e.g. features

##### Adaptive_Design_Helper

The DeviceDetector helper makes it easy to implement the adaptive design approach in Pimcore.

- [Adaptive Design Helper](/Pimcore/Development_Tools_and_Details/Adaptive_Design_Helper.md): The DeviceDetector helper makes it easy to implement the adaptive design approach in Pimcore.

##### Cache

Pimcore uses extensively caches for differently types of data. The primary cache is a pure object

- [Cache](/Pimcore/Development_Tools_and_Details/Cache.md): Pimcore uses extensively caches for differently types of data. The primary cache is a pure object
- [Full Page Cache (Output Cache)](/Pimcore/Development_Tools_and_Details/Cache/Full_Page_Cache.md): Overview

##### Console_CLI

Pimcore can be executed headless and has a very powerful PHP API. As a consequence of these two aspects,

- [CLI and Pimcore Console](/Pimcore/Development_Tools_and_Details/Console_CLI.md): Pimcore can be executed headless and has a very powerful PHP API. As a consequence of these two aspects,

##### Custom_Admin_Login_Entry_Point

Pimcore /admin login entry point can be restricted/changed by using pimcore configuration.

- [Custom Admin Login Entry Point](/Pimcore/Development_Tools_and_Details/Custom_Admin_Login_Entry_Point.md): Pimcore /admin login entry point can be restricted/changed by using pimcore configuration.

##### Database_Model

Pimcore tries to keep a clean and optimized database model for managing the data. Nevertheless,

- [Database Model](/Pimcore/Development_Tools_and_Details/Database_Model.md): Pimcore tries to keep a clean and optimized database model for managing the data. Nevertheless,

##### Debugging

In this chapter, a few insights, tips and tricks for debugging Pimcore are shown. This should give you a

- [Debugging Pimcore](/Pimcore/Development_Tools_and_Details/Debugging.md): In this chapter, a few insights, tips and tricks for debugging Pimcore are shown. This should give you a

##### Email_Framework

General Information

- [Email Framework](/Pimcore/Development_Tools_and_Details/Email_Framework.md): General Information
- [Pimcore Mail](/Pimcore/Development_Tools_and_Details/Email_Framework/Pimcore_Mail.md): The Pimcore\Mail Class extends the Symfony\Component\Mime\Email

##### Extending_a_Backend_User

Pimcore does not allow to extend the user directly. Instead it allows to create a relation

- [Extending the Pimcore User](/Pimcore/Development_Tools_and_Details/Extending_a_Backend_User.md): Pimcore does not allow to extend the user directly. Instead it allows to create a relation

##### Generic_Execution_Engine

Overview

- [Generic Execution Engine](/Pimcore/Development_Tools_and_Details/Generic_Execution_Engine.md): Overview
- [Configuration of the Generic Execution Engine](/Pimcore/Development_Tools_and_Details/Generic_Execution_Engine/Configuration.md): The Generic Execution Engine is configured via Symfony configuration files. Default configuration looks like this:
- [Extending Generic Execution Engine](/Pimcore/Development_Tools_and_Details/Generic_Execution_Engine/Extending/Extending_Generic_Execution_Engine.md): Extending Generic Execution Engine via Events
- [Working with the Generic Execution Engine](/Pimcore/Development_Tools_and_Details/Generic_Execution_Engine/Jobs_and_Jobruns.md): Working with the Generic Execution Engine consists of the several steps.
- [JobRun](/Pimcore/Development_Tools_and_Details/Generic_Execution_Engine/Jobs_and_Jobruns/JobRun.md): Job Runs
- [Jobs](/Pimcore/Development_Tools_and_Details/Generic_Execution_Engine/Jobs_and_Jobruns/Jobs.md): Define Jobs
- [Step configuration](/Pimcore/Development_Tools_and_Details/Generic_Execution_Engine/Jobs_and_Jobruns/Step_Configuration.md): The configuration of a step is done via the JobStep object.

##### Logging

There are several different kinds of logs in Pimcore. All of them are located under /var/log and get rotated

- [Logging](/Pimcore/Development_Tools_and_Details/Logging.md): There are several different kinds of logs in Pimcore. All of them are located under /var/log and get rotated

##### Magic_Parameters

Pimcore supports some magic parameters which can be added as parameter to every request.

- [Magic Parameters](/Pimcore/Development_Tools_and_Details/Magic_Parameters.md): Pimcore supports some magic parameters which can be added as parameter to every request.

##### Migrations

A common tasks in evolving applications is the need to migrate data and data structures to a specific format. Common examples

- [Migrations](/Pimcore/Development_Tools_and_Details/Migrations.md): A common tasks in evolving applications is the need to migrate data and data structures to a specific format. Common examples

##### Preview_Scheduled_Content

In Document preview tab Pimcore can provide a time slider to preview content at any given time.

- [Preview Scheduled Content](/Pimcore/Development_Tools_and_Details/Preview_Scheduled_Content.md): In Document preview tab Pimcore can provide a time slider to preview content at any given time.

##### Security_Authentication

You can make full use of the Symfony Security Component to handle complex

- [Security and Authentication](/Pimcore/Development_Tools_and_Details/Security_Authentication.md): You can make full use of the Symfony Security Component to handle complex
- [Authenticate Against Pimcore Objects](/Pimcore/Development_Tools_and_Details/Security_Authentication/Authenticate_Pimcore_Objects.md): As Symfony's security component is quite complex, Pimcore provides base implementations to facilitate integrating the security
- [Authenticator Based Security](/Pimcore/Development_Tools_and_Details/Security_Authentication/Authenticator_Based_Security.md): Note: This feature is available since v10.5
- [Configure Password Hashing Algorithm](/Pimcore/Development_Tools_and_Details/Security_Authentication/Configure_Password_Hashing_Algorithm.md): Pimcore uses PHP's default password hashing algorithm by default, which currently equals to BCrypt with a cost of 10

##### Settings_Store

The settings store is a simple key value store and allows to persist any kind of settings into the

- [Settings Store](/Pimcore/Development_Tools_and_Details/Settings_Store.md): The settings store is a simple key value store and allows to persist any kind of settings into the

##### Static_Helpers

Pimcore offers some static helpers:

- [Static Helpers](/Pimcore/Development_Tools_and_Details/Static_Helpers.md): Pimcore offers some static helpers:

##### Static_Page_Generator

Pimcore offers a Static Page Generator service, which is used to generate HTML pages from Pimcore documents. This generator service works by taking a Pimcore document with content and templates and renders them into a full HTML page, that can served directly from the server without the intervention of templating engine.

- [Static Page Generator](/Pimcore/Development_Tools_and_Details/Static_Page_Generator.md): Pimcore offers a Static Page Generator service, which is used to generate HTML pages from Pimcore documents. This generator service works by taking a Pimcore document with content and templates and renders them into a full HTML page, that can served directly from the server without the intervention of templating engine.

##### Testing

If you want to get a detailed introduction on how Pimcore applications can be tested have a look at

- [Testing](/Pimcore/Development_Tools_and_Details/Testing.md): If you want to get a detailed introduction on how Pimcore applications can be tested have a look at
- [Application Testing](/Pimcore/Development_Tools_and_Details/Testing/Application_Testing.md): Pimcore applications can be tested with any PHP testing solution, but this page demonstrates 2 viable approaches:
- [Core Testing](/Pimcore/Development_Tools_and_Details/Testing/Core_Tests.md): Pimcore uses Codeception for testing its core features.
- [Testing Symfony Services](/Pimcore/Development_Tools_and_Details/Testing/Testing_Services.md): For integration tests of symfony services in context of their configuration in container, there are multiple ways for

##### UUID_Support

Note

- [UUID Support](/Pimcore/Development_Tools_and_Details/UUID_Support.md): Note

##### Working_with_Sessions

If you need sessions, please use the native session handling provided by Symfony (configured through the framework.session config).

- [Working With Sessions](/Pimcore/Development_Tools_and_Details/Working_with_Sessions.md): If you need sessions, please use the native session handling provided by Symfony (configured through the framework.session config).

#### Documents

Documents are the CMS part of Pimcore and are the way to go for managing unstructured contents using pages, content snippets and navigations.

- [Documents](/Pimcore/Documents.md): Documents are the CMS part of Pimcore and are the way to go for managing unstructured contents using pages, content snippets and navigations.

##### Editables

The editables are placeholders in the templates, which are displayed as input widgets in the admin interface (so called editmode) and output the content in frontend mode.

- [Editables](/Pimcore/Documents/Editables.md): The editables are placeholders in the templates, which are displayed as input widgets in the admin interface (so called editmode) and output the content in frontend mode.
- [Area Editable](/Pimcore/Documents/Editables/Area.md): General
- [Areablock Editable](/Pimcore/Documents/Editables/Areablock.md): General
- [Create Your Own Bricks](/Pimcore/Documents/Editables/Areablock/Bricks.md): Architecture of a Brick
- [Block Editable](/Pimcore/Documents/Editables/Block.md): General
- [Checkbox Editable](/Pimcore/Documents/Editables/Checkbox.md): Configuration
- [Date Editable](/Pimcore/Documents/Editables/Date.md): Configuration
- [Embed Editable](/Pimcore/Documents/Editables/Embed.md): Configuration
- [Image Editable](/Pimcore/Documents/Editables/Image.md): Description
- [Input Editable](/Pimcore/Documents/Editables/Input.md): General
- [Link Editable](/Pimcore/Documents/Editables/Link.md): General
- [Multiselect Editable](/Pimcore/Documents/Editables/Multiselect.md): General
- [Numeric Editable](/Pimcore/Documents/Editables/Numeric.md): General
- [PDF Editable](/Pimcore/Documents/Editables/PDF.md): This editable requires Ghostscript installed on your server.
- [Relation (Many-To-One) Editable](/Pimcore/Documents/Editables/Relation_Many-To-One.md): General
- [Relations (Many-To-Many) Editable](/Pimcore/Documents/Editables/Relations_Many-To-Many.md): General
- [Renderlet Editable](/Pimcore/Documents/Editables/Renderlet.md): General
- [Scheduled Block Editable](/Pimcore/Documents/Editables/Scheduled_Block.md): General
- [Select Editable](/Pimcore/Documents/Editables/Select.md): General
- [Snippet Editable](/Pimcore/Documents/Editables/Snippet.md): General
- [Table Editable](/Pimcore/Documents/Editables/Table.md): General
- [Textarea Editable](/Pimcore/Documents/Editables/Textarea.md): General
- [Video Editable](/Pimcore/Documents/Editables/Video.md): General
- [WYSIWYG Editable](/Pimcore/Documents/Editables/WYSIWYG.md): General

##### Inheritance

The Content Main Document setting, allows a document to inherit all of its contents from any other document which can

- [Document Inheritance](/Pimcore/Documents/Inheritance.md): The Content Main Document setting, allows a document to inherit all of its contents from any other document which can

##### Navigation

Basics

- [Navigation](/Pimcore/Documents/Navigation.md): Basics

##### Predefined_Document_Types

General

- [Predefined Document-Types](/Pimcore/Documents/Predefined_Document_Types.md): General

##### Working_with_PHP_API

Pimcore provides the object orientated PHP API to work with Documents.

- [Working With Documents via PHP API](/Pimcore/Documents/Working_with_PHP_API.md): Pimcore provides the object orientated PHP API to work with Documents.

#### Extending_Pimcore

When building solutions with Pimcore, normally one starts with configuring an object data model,

- [Extending Pimcore](/Pimcore/Extending_Pimcore.md): When building solutions with Pimcore, normally one starts with configuring an object data model,

##### Add_Your_Own_Dependencies_and_Packages

Pimcore manages itself all dependencies using composer and therefore you can add your own dependencies by using

- [Add Your Own Dependencies and Packages](/Pimcore/Extending_Pimcore/Add_Your_Own_Dependencies_and_Packages.md): Pimcore manages itself all dependencies using composer and therefore you can add your own dependencies by using

##### Add_Your_Own_Permissions

Add your permission to the database

- [Add Your Own Permissions](/Pimcore/Extending_Pimcore/Add_Your_Own_Permissions.md): Add your permission to the database

##### Bundle_Developers_Guide

Since Pimcore utilizes the powerful Symfony Bundle system, let us refer to the Symfony Bundle Documentation on how to get started with your custom bundles. A bundle can do anything - in fact, core Pimcore functionalities like the admin interface are implemented as bundles. From within your bundle, you have all possibilities to extend the system, from defining new services or routes to hook into the event system or provide controllers and views.

- [Bundle Developer's Guide](/Pimcore/Extending_Pimcore/Bundle_Developers_Guide.md): Since Pimcore utilizes the powerful Symfony Bundle system, let us refer to the Symfony Bundle Documentation on how to get started with your custom bundles. A bundle can do anything - in fact, core Pimcore functionalities like the admin interface are implemented as bundles. From within your bundle, you have all possibilities to extend the system, from defining new services or routes to hook into the event system or provide controllers and views.
- [Adding Asset Types](/Pimcore/Extending_Pimcore/Bundle_Developers_Guide/Adding_Asset_Types.md): This feature allows users to add their own custom asset types.
- [Adding Document Editables](/Pimcore/Extending_Pimcore/Bundle_Developers_Guide/Adding_Document_Editables.md): With bundles, it is also possible to add an individual Document Editable.
- [Adding Document Types](/Pimcore/Extending_Pimcore/Bundle_Developers_Guide/Adding_Document_Types.md): Defining custom documents can be done in the config via a static mapping from document type to class name.
- [Adding Object Layout Types](/Pimcore/Extending_Pimcore/Bundle_Developers_Guide/Adding_Object_ Layout_types.md): Note: This feature is available since v6.6.1
- [Adding Object Datatypes](/Pimcore/Extending_Pimcore/Bundle_Developers_Guide/Adding_Object_Datatypes.md): With plugins, it is also possible to add individual data types for Pimcore Objects.
- [Auto Loading Config and Routing Definitions](/Pimcore/Extending_Pimcore/Bundle_Developers_Guide/Auto_Loading_Config_And_Routing_Definitions.md): By default, Symfony does not load configuration and/or routing definitions from bundles but expects you to define everything
- [Bundle Collection](/Pimcore/Extending_Pimcore/Bundle_Developers_Guide/Bundle_Collection.md): The BundleCollection is a container which is used to register every used bundle. As Pimcore gathers bundles from multiple
- [Event Listener UI](/Pimcore/Extending_Pimcore/Bundle_Developers_Guide/Event_Listener_UI.md): General
- [Loading Assets in the Admin UI](/Pimcore/Extending_Pimcore/Bundle_Developers_Guide/Loading_Admin_UI_Assets.md): If you need to load assets (JS, CSS) in the Admin or Editmode UI, you have 2 options, depending on if you do that from a
- [Loading Service Definitions From Within a Bundle](/Pimcore/Extending_Pimcore/Bundle_Developers_Guide/Loading_Service_Definitions.md): If you want to load services from your bundle instead of having to define them in config/services.yaml you need to
- [Pimcore Bundles](/Pimcore/Extending_Pimcore/Bundle_Developers_Guide/Pimcore_Bundles.md): Pimcore bundles follow the same rules as normal bundles, but need to implement Pimcore\Extension\Bundle\PimcoreBundleInterface
- [Installers](/Pimcore/Extending_Pimcore/Bundle_Developers_Guide/Pimcore_Bundles/Installers.md): Besides being enabled, bundles may need to execute installation tasks in order to be fully functional. This may concern

##### Custom_Persistent_Models

When to use Custom Models

- [Custom Persistent Models](/Pimcore/Extending_Pimcore/Custom_Persistent_Models.md): When to use Custom Models

##### Deeplinks_into_Admin_Interface

Pimcore offers the possibility to deeplink elements inside the admin-interface from an external application.

- [Deeplinks Into Admin-Interface](/Pimcore/Extending_Pimcore/Deeplinks_into_Admin_Interface.md): Pimcore offers the possibility to deeplink elements inside the admin-interface from an external application.

##### Dependency_Injection_Tags

Please read the intro of dependency injection tags of Symfony first.

- [Dependency Injection Tags](/Pimcore/Extending_Pimcore/Dependency_Injection_Tags.md): Please read the intro of dependency injection tags of Symfony first.

##### Event_API_and_Event_Manager

General

- [Events and Event Listeners](/Pimcore/Extending_Pimcore/Event_API_and_Event_Manager.md): General

##### Implement_Your_Own_Search

Register Implementation

- [Implement Your Own Search](/Pimcore/Extending_Pimcore/Implement_Your_Own_Search.md): Register Implementation

##### Maintenance_Mode

Pimcore offers a maintenance mode, which restricts access to the admin user interface to the user that enabled the maintenance mode. It is session based

- [Maintenance Mode](/Pimcore/Extending_Pimcore/Maintenance_Mode.md): Pimcore offers a maintenance mode, which restricts access to the admin user interface to the user that enabled the maintenance mode. It is session based

##### Maintenance_Tasks

Pimcore offers you to run scheduled maintenance tasks. This allows you to periodically do stuff like cleanups.

- [Maintenance Tasks](/Pimcore/Extending_Pimcore/Maintenance_Tasks.md): Pimcore offers you to run scheduled maintenance tasks. This allows you to periodically do stuff like cleanups.

##### Overriding_Models

Sometimes it is necessary to override certain functionalities of Pimcore's core models, therefore it is possible to

- [Overriding Models / Entities in Pimcore](/Pimcore/Extending_Pimcore/Overriding_Models.md): Sometimes it is necessary to override certain functionalities of Pimcore's core models, therefore it is possible to

##### Parent_Class_for_Objects

In addition to overriding model classes

- [Parent Class for Objects](/Pimcore/Extending_Pimcore/Parent_Class_for_Objects.md): In addition to overriding model classes

#### Getting_Started

If you're planning to use the free Pimcore Community Edition. please read the

- [Getting Started With Pimcore](/Pimcore/Getting_Started.md): If you're planning to use the free Pimcore Community Edition. please read the

##### Advanced_Installation_Topics

To fully automate the installation process, options can be passed in the CLI as parameters, rather than adding them interactively.

- [Advanced Installation Topics](/Pimcore/Getting_Started/Advanced_Installation_Topics.md): To fully automate the installation process, options can be passed in the CLI as parameters, rather than adding them interactively.
- [Symfony Messenger](/Pimcore/Getting_Started/Advanced_Installation_Topics/Symfony_Messenger.md): Handle Failed Jobs

##### Architecture_Overview

At this point we want to give a short overview of the architecture of Pimcore.

- [Architecture Overview](/Pimcore/Getting_Started/Architecture_Overview.md): At this point we want to give a short overview of the architecture of Pimcore.

##### Configuration

Pimcore's configuration can be found in several places:

- [Configuration](/Pimcore/Getting_Started/Configuration.md): Pimcore's configuration can be found in several places:

##### Create_a_First_Project

In this section, you will learn the basics of Pimcore, required to start developing.

- [Create a First Project](/Pimcore/Getting_Started/Create_a_First_Project.md): In this section, you will learn the basics of Pimcore, required to start developing.

##### Directory_Structure

After installing a Pimcore package you should see the folder structure described below.

- [Directory Structure](/Pimcore/Getting_Started/Directory_Structure.md): After installing a Pimcore package you should see the folder structure described below.

##### Installation

- [Docker-Based Installation](/Pimcore/Getting_Started/Installation/Docker_Based_Installation.md): You can use Docker to set up a new Pimcore Installation.
- [Webserver Installation](/Pimcore/Getting_Started/Installation/Webserver_Installation.md): The following guide assumes you're using a typical LAMP environment. If you're using a different setup (eg. Nginx) or you're facing a problem, please visit the Installation Guide section.

##### Product_Registration

To ensure secure and compliant usage, Pimcore requires product registration before it can be installed or used.

- [Product Registration](/Pimcore/Getting_Started/Product_Registration.md): To ensure secure and compliant usage, Pimcore requires product registration before it can be installed or used.

#### Installation_and_Upgrade

General Topics

- [Installation, Setup and Upgrade](/Pimcore/Installation_and_Upgrade.md): General Topics

##### System_Requirements

Server Requirements

- [System Requirements](/Pimcore/Installation_and_Upgrade/System_Requirements.md): Server Requirements

##### System_Setup_and_Hosting

General Topics

- [System Setup & Hosting/Infrastructure Topics](/Pimcore/Installation_and_Upgrade/System_Setup_and_Hosting.md): General Topics
- [Additional Tools Installation](/Pimcore/Installation_and_Upgrade/System_Setup_and_Hosting/Additional_Tools_Installation.md): Pimcore uses some 3rd party applications for certain functionalities, such as video transcoding (FFMPEG), image optimization (advpng, cjpeg, ...), and many others. For a full list of additional tools required or recommended for Pimcore, please visit Pimcore System Requirements.
- [Apache Configuration](/Pimcore/Installation_and_Upgrade/System_Setup_and_Hosting/Apache_Configuration.md): .htaccess
- [Database Setup](/Pimcore/Installation_and_Upgrade/System_Setup_and_Hosting/DB_Setup.md): Pimcore requires a standard MySQL database, the only thing you should assure is that the database uses utf8mb4 as character set.
- [File Permissions](/Pimcore/Installation_and_Upgrade/System_Setup_and_Hosting/File_Permissions.md): Pimcore requires write access to the following directories: /var and /public/var.
- [File Storage Setup](/Pimcore/Installation_and_Upgrade/System_Setup_and_Hosting/File_Storage_Setup.md): Pimcore uses a powerful & flexible file storage library, called Flysystem
- [Fix Performance Issues on Windows](/Pimcore/Installation_and_Upgrade/System_Setup_and_Hosting/Fix_Performance_Issues_on_Windows.md): It is highly recommended not to use Windows based systems in production!
- [Multi-application setup](/Pimcore/Installation_and_Upgrade/System_Setup_and_Hosting/Multi_Application_Setup.md): Sessions
- [Nginx Configuration](/Pimcore/Installation_and_Upgrade/System_Setup_and_Hosting/Nginx_Configuration.md): Installation on Nginx is entirely possible, and in our experience quite a lot faster than apache. This section won't dive into how Nginx is installed etc, but will show a working Nginx configuration.
- [Performance Best-Practice Guide](/Pimcore/Installation_and_Upgrade/System_Setup_and_Hosting/Performance_Guide.md): When developing a high traffic web application, it is a common practice to focus on performance measures and

##### Updating_Pimcore

Our Backward Compatibility Promise

- [Updating Pimcore](/Pimcore/Installation_and_Upgrade/Updating_Pimcore.md): Our Backward Compatibility Promise
- [Preparing Pimcore for Version 11](/Pimcore/Installation_and_Upgrade/Updating_Pimcore/Preparing_for_V11.md): Upgrade to Pimcore 10.6
- [Upgrading Pimcore from Version 10.x to Version 11](/Pimcore/Installation_and_Upgrade/Updating_Pimcore/V10_to_V11.md): Tasks to Do Prior the Update
- [Upgrading Pimcore from Version 11.x to Version 12](/Pimcore/Installation_and_Upgrade/Updating_Pimcore/V11_to_V12.md): IMPORTANT
- [Upgrading Pimcore from Version 6.x to Version 10](/Pimcore/Installation_and_Upgrade/Updating_Pimcore/V6_to_V10.md): System Requirement changes

##### Upgrade_Notes

Pimcore 12.2.0

- [Upgrade Notes](/Pimcore/Installation_and_Upgrade/Upgrade_Notes.md): Pimcore 12.2.0

#### Multi_Language_i18n

Localization is a very important aspect in every content management. In Pimcore localization can be centrally configured

- [Multi Language i18n & Localization in Pimcore](/Pimcore/Multi_Language_i18n.md): Localization is a very important aspect in every content management. In Pimcore localization can be centrally configured

##### Admin_Translations

There are several components in the Pimcore backend UI which are configured differently for each project. These are

- [Admin Translations](/Pimcore/Multi_Language_i18n/Admin_Translations.md): There are several components in the Pimcore backend UI which are configured differently for each project. These are

##### Formatting_Service

Pimcore ships a service for international formatting of numbers, currencies and date time. The service is basically a

- [Formatting Service](/Pimcore/Multi_Language_i18n/Formatting_Service.md): Pimcore ships a service for international formatting of numbers, currencies and date time. The service is basically a

##### Localize_your_Documents

Pimcore allows you to localize every document. You can find the setting in your document in the tab Properties.

- [Localize Your Documents](/Pimcore/Multi_Language_i18n/Localize_your_Documents.md): Pimcore allows you to localize every document. You can find the setting in your document in the tab Properties.

##### Shared_Translations

Pimcore provides an easy way for editors to edit commonly used translation terms across the application, which can be found

- [Shared Translations](/Pimcore/Multi_Language_i18n/Shared_Translations.md): Pimcore provides an easy way for editors to edit commonly used translation terms across the application, which can be found

#### MVC

In terms of sending output to the frontend, Pimcore follows the MVC pattern.

- [MVC in Pimcore](/Pimcore/MVC.md): In terms of sending output to the frontend, Pimcore follows the MVC pattern.

##### Controller

Introduction

- [Pimcore Controller](/Pimcore/MVC/Controller.md): Introduction

##### Routing_and_URLs

Introduction

- [Routing and URLs](/Pimcore/MVC/Routing_and_URLs.md): Introduction
- [URLs Based on Custom (Static) Routes](/Pimcore/MVC/Routing_and_URLs/Custom_Routes.md): To use this feature, please enable the PimcoreStaticRoutesBundle in your bundle.php file and install it accordingly with the following command:
- [URLs Based on Documents and Pretty URLs](/Pimcore/MVC/Routing_and_URLs/Documents_and_Pretty_URLs.md): Introduction
- [URLs Based on Redirects](/Pimcore/MVC/Routing_and_URLs/Redirects.md): To use this feature, please enable the PimcoreSeoBundle in your bundle.php file and install it accordingly with the following command:
- [Working With Sites](/Pimcore/MVC/Routing_and_URLs/Working_with_Sites.md): Introduction

##### Template

Introduction

- [Pimcore Templates](/Pimcore/MVC/Template.md): Introduction
- [Template Inheritance and Layouts](/Pimcore/MVC/Template/Layouts.md): Introduction
- [Twig Extensions](/Pimcore/MVC/Template/Template_Extensions.md): Introduction
- [HeadLink Templating Extension](/Pimcore/MVC/Template/Template_Extensions/HeadLink.md): The HeadLink templating extension extends Placeholder Templating Extension
- [HeadMeta Templating Extension](/Pimcore/MVC/Template/Template_Extensions/HeadMeta.md): The HeadMeta templating extension extends Placeholder Templating Extension
- [HeadScript Templating Extension](/Pimcore/MVC/Template/Template_Extensions/HeadScript.md): The HeadScript templating extension extends Placeholder Templating Extension
- [HeadStyle Templating Extension](/Pimcore/MVC/Template/Template_Extensions/HeadStyle.md): The HeadStyle templating extension extends Placeholder Templating Extension
- [HeadTitle Templating Extension](/Pimcore/MVC/Template/Template_Extensions/HeadTitle.md): The HeadTitle templating extension extends Placeholder Templating Extension
- [InlineScript Template Extension](/Pimcore/MVC/Template/Template_Extensions/InlineScript.md): The InlineScript template extension extends Placeholder Templating Extension
- [Placeholder Templating Extension](/Pimcore/MVC/Template/Template_Extensions/Placeholder.md): The Placeholder extension is used to persist content between view scripts and view instances. It also offers
- [Pimcore Thumbnails](/Pimcore/MVC/Template/Thumbnails.md): Introduction

#### Objects

Objects are the PIM part of Pimcore and are the way to go for managing structured data within Pimcore. Based on a class

- [Objects](/Pimcore/Objects.md): Objects are the PIM part of Pimcore and are the way to go for managing structured data within Pimcore. Based on a class

##### Customize_Editing_Interface

Customize Add Object Dialog

- [Customize Editing Interface](/Pimcore/Objects/Customize_Editing_Interface.md): Customize Add Object Dialog

##### External_System_Interaction

Whenever interaction with other systems is required, data objects are the vital components of data exchange.

- [External System Interaction](/Pimcore/Objects/External_System_Interaction.md): Whenever interaction with other systems is required, data objects are the vital components of data exchange.

##### Object_Classes

To get started with Pimcore objects, classes must be defined.

- [Object Classes](/Pimcore/Objects/Object_Classes.md): To get started with Pimcore objects, classes must be defined.
- [Additional Class Settings](/Pimcore/Objects/Object_Classes/Class_Settings.md): On class level, there are multiple additional settings and features available which influence the appearance and behaviour of Pimcore. These can be either configured in the Pimcore
- [Composite Indices](/Pimcore/Objects/Object_Classes/Class_Settings/Composite_Indices.md): Pimcore can create composite indices on objectquery, object_store_, objectlocalizeddata and object_localized_query tables for you.
- [Custom Icons for Objects](/Pimcore/Objects/Object_Classes/Class_Settings/Custom_Icons.md): Pimcore allows you to define custom icons for objects. Either, icons can be the same for all objects of a class
- [Custom Layouts](/Pimcore/Objects/Object_Classes/Class_Settings/Custom_Layouts.md): It is possible to create customized layouts based on the main definition and override the settings concerning the visual aspects of the layout and data components. It is also possible to make a field editable, although it is
- [Custom View Example Configuration](/Pimcore/Objects/Object_Classes/Class_Settings/Custom_View_Example.md)
- [Custom Views](/Pimcore/Objects/Object_Classes/Class_Settings/Custom_Views.md): A custom view is an additional custom tree representing a subset of elements of the corresponding original tree.
- [Data Inheritance and Parent Class](/Pimcore/Objects/Object_Classes/Class_Settings/Inheritance.md): Pimcore provides two sorts of inheritance. While data inheritance allows the inheritance of object data along the tree
- [Using Interfaces and Traits](/Pimcore/Objects/Object_Classes/Class_Settings/Interfaces_and_traits.md): In some cases it could be helpful to let the generated PHP class for data objects implement interfaces or add some additional functions using traits.
- [Link Generator](/Pimcore/Objects/Object_Classes/Class_Settings/Link_Generator.md): Summary
- [Locking Fields](/Pimcore/Objects/Object_Classes/Class_Settings/Locking_Fields.md): Sometimes it's useful that a field cannot be modified/deleted in the class editor. Especially if a class is
- [Path Formatter](/Pimcore/Objects/Object_Classes/Class_Settings/Path_Formatter.md): Summary
- [Preview Generator](/Pimcore/Objects/Object_Classes/Class_Settings/Preview_Generator.md): Summary
- [Object Variants](/Pimcore/Objects/Object_Classes/Class_Settings/Variants.md): The best way to show the use and function of object variants is via a use case:
- [Object Data Types](/Pimcore/Objects/Object_Classes/Data_Types.md): The entire list of data types is indicated below:
- [Blocks](/Pimcore/Objects/Object_Classes/Data_Types/Blocks.md): The block data type acts as a simple container for other data fields.
- [Calculated Value Datatype](/Pimcore/Objects/Object_Classes/Data_Types/Calculated_Value_Type.md): General
- [Classification Store](/Pimcore/Objects/Object_Classes/Data_Types/Classification_Store.md): Overview
- [Consent](/Pimcore/Objects/Object_Classes/Data_Types/Consent.md): This data type can be used to store consent of users for something like permission for sending direct mailings.
- [Date Datatypes](/Pimcore/Objects/Object_Classes/Data_Types/Date_Types.md): Date, Date & Time
- [Select Types With Dynamic Options](/Pimcore/Objects/Object_Classes/Data_Types/Dynamic_Select_Types.md): For the select & multiselect datatype you can specify a dynamic options provider class.
- [Fieldcollection](/Pimcore/Objects/Object_Classes/Data_Types/Fieldcollections.md): General Usage
- [Geographic Datatypes](/Pimcore/Objects/Object_Classes/Data_Types/Geographic_Types.md): There are different geographic data types available in pimcore: Geopoint, Geobounds, Geopolygon and Geopolyline.
- [Image Datatypes](/Pimcore/Objects/Object_Classes/Data_Types/Image_Types.md): Image
- [Limitations](/Pimcore/Objects/Object_Classes/Data_Types/Limitations.md): Limitations
- [Localized Fields](/Pimcore/Objects/Object_Classes/Data_Types/Localized_Fields.md): Localized fields allow the definition of attributes, that should be translated into multiple languages within an object.
- [Number Datatypes](/Pimcore/Objects/Object_Classes/Data_Types/Number_Types.md): Both numeric data types (number and slider) are stored as a number in a DOUBLE column in the database.
- [Objects Bricks](/Pimcore/Objects/Object_Classes/Data_Types/Object_Bricks.md): General
- [Other Datatypes](/Pimcore/Objects/Object_Classes/Data_Types/Others.md): Checkbox
- [Relational Datatypes](/Pimcore/Objects/Object_Classes/Data_Types/Relation_Types.md): Many-To-One, Many-To-Many and Many-To-Many Object Relation Data Fields
- [Reverse Object Relation Datatype](/Pimcore/Objects/Object_Classes/Data_Types/Reverse_Object_Relation_Type.md): Reverse Object Relation are the counter part to the Many-To-Many Object Relation & Many-To-One Object Relation fields.
- [Select Options](/Pimcore/Objects/Object_Classes/Data_Types/Select_Options.md): Select options are predefined sets of options which may be used for (multi)select fields.
- [Select Datatypes](/Pimcore/Objects/Object_Classes/Data_Types/Select_Types.md): There are 7 different select widgets available. Except the Multiselect widgets, all of them are portrayed by an input
- [Structured Table](/Pimcore/Objects/Object_Classes/Data_Types/Structured_Table.md): Add Structured Table to the Class
- [Table](/Pimcore/Objects/Object_Classes/Data_Types/Table.md): The table widget can hold structured data in the form of an array.
- [Text Datatypes](/Pimcore/Objects/Object_Classes/Data_Types/Text_Types.md): Input
- [Video Datatype](/Pimcore/Objects/Object_Classes/Data_Types/Video_Type.md): Video Field
- [Layout Elements](/Pimcore/Objects/Object_Classes/Layout_Elements.md): To structure object data layout-wise, there are 3 panel types and 4 other layout elements available. Data fields are
- [Dynamic Text Labels](/Pimcore/Objects/Object_Classes/Layout_Elements/Dynamic_Text_Labels.md): Similar to the CalculatedValue data type,
- [Preview / Iframe Panel](/Pimcore/Objects/Object_Classes/Layout_Elements/Preview_Iframe.md): Provide a URL and make use of the context paramater to render a response of your choice.

##### Working_with_PHP_API

Pimcore provides an object orientated PHP API to work with Objects. There are several generic functionalities

- [Working With Objects via PHP API](/Pimcore/Objects/Working_with_PHP_API.md): Pimcore provides an object orientated PHP API to work with Objects. There are several generic functionalities

#### Overview

Pimcore is the leading Open Core platform for managing digital data and customer experience. It provides a fully integrated software stack for PIM, MDM, CDP, DAM, DXP/CMS & Digital Commerce.

- [Pimcore Overview](/Pimcore/Overview.md): Pimcore is the leading Open Core platform for managing digital data and customer experience. It provides a fully integrated software stack for PIM, MDM, CDP, DAM, DXP/CMS & Digital Commerce.

#### Tools_and_Features

This section of documentation gives an overview over tools and features provided in or for the Pimcore backend UI but also can

- [Tools and Features](/Pimcore/Tools_and_Features.md): This section of documentation gives an overview over tools and features provided in or for the Pimcore backend UI but also can

##### Appearance_and_Branding

In the appearance & branding settings (Settings > Appearance & Branding) system-wide settings for Pimcore Admin Interface can be made. Changes should

- [Appearance & Branding](/Pimcore/Tools_and_Features/Appearance_and_Branding.md): In the appearance & branding settings (Settings > Appearance & Branding) system-wide settings for Pimcore Admin Interface can be made. Changes should

##### Application_Logger

To use this feature, please enable the PimcoreApplicationLoggerBundle in your bundle.php file and install it accordingly with the following command:

- [Application logger](/Pimcore/Tools_and_Features/Application_Logger.md): To use this feature, please enable the PimcoreApplicationLoggerBundle in your bundle.php file and install it accordingly with the following command:

##### Auto_Save_Drafts

Pimcore has an auto save functionality enabled by default for Data Objects and Documents, which creates a draft on default interval of 60 seconds or as soon as there is a first change detected on a data object/document.

- [Auto Save Drafts](/Pimcore/Tools_and_Features/Auto_Save_Drafts.md): Pimcore has an auto save functionality enabled by default for Data Objects and Documents, which creates a draft on default interval of 60 seconds or as soon as there is a first change detected on a data object/document.

##### Cloning Elements

Use

- [Cloning Elements](/Pimcore/Tools_and_Features/Cloning Elements.md): Use

##### Custom_Icons

Pimcore allows to dynamically define custom element icons & tooltips in the element tree. In addition, the icon of the editor tab can

- [Custom Icons & Tooltips for Documents/Assets & Data Objects](/Pimcore/Tools_and_Features/Custom_Icons.md): Pimcore allows to dynamically define custom element icons & tooltips in the element tree. In addition, the icon of the editor tab can

##### Custom_Reports

To use this feature, please enable the PimcoreCustomReportsBundle in your bundles.php file via:

- [Custom Reports](/Pimcore/Tools_and_Features/Custom_Reports.md): To use this feature, please enable the PimcoreCustomReportsBundle in your bundles.php file via:

##### GDPR_Data_Extractor

The GDPR Data Extractor is a tool that helps the user to full fill the right of access by the data subject and helps to

- [GDPR Data Extractor](/Pimcore/Tools_and_Features/GDPR_Data_Extractor.md): The GDPR Data Extractor is a tool that helps the user to full fill the right of access by the data subject and helps to

##### Glossary

To use this feature, please enable the PimcoreGlossaryBundle in your bundle.php file and install it accordingly with the following command:

- [Glossary](/Pimcore/Tools_and_Features/Glossary.md): To use this feature, please enable the PimcoreGlossaryBundle in your bundle.php file and install it accordingly with the following command:

##### Notes_and_Events

General

- [Notes & Events](/Pimcore/Tools_and_Features/Notes_and_Events.md): General

##### Notifications

Feature allows to send notifications to user. Notifications adds to status bar new clickable icon, on click it opens new tab with all notifications, also it contains badge with unread notifications count.

- [Notifications](/Pimcore/Tools_and_Features/Notifications.md): Feature allows to send notifications to user. Notifications adds to status bar new clickable icon, on click it opens new tab with all notifications, also it contains badge with unread notifications count.

##### Permission_Analyzer

Allows you to drop an element (plus optionally a user) and display the calculated workspace permissions.

- [Permission Analyzer](/Pimcore/Tools_and_Features/Permission_Analyzer.md): Allows you to drop an element (plus optionally a user) and display the calculated workspace permissions.

##### Perspective_Example

- [Perspective Example Configuration](/Pimcore/Tools_and_Features/Perspective_Example.md)

##### Perspectives

General

- [Perspectives](/Pimcore/Tools_and_Features/Perspectives.md): General

##### Properties

General

- [Properties](/Pimcore/Tools_and_Features/Properties.md): General

##### Rendering_PDFs

Instead of directly returning the HTML code of your website you could also return a PDF version.

- [Rendering PDFs](/Pimcore/Tools_and_Features/Rendering_PDFs.md): Instead of directly returning the HTML code of your website you could also return a PDF version.

##### Robots.txt

To use this feature, please enable the PimcoreSeoBundle in your bundle.php file and install it accordingly with the following command:

- [Robots.txt](/Pimcore/Tools_and_Features/Robots.txt.md): To use this feature, please enable the PimcoreSeoBundle in your bundle.php file and install it accordingly with the following command:

##### Scheduling

General

- [Scheduling](/Pimcore/Tools_and_Features/Scheduling.md): General

##### Sitemaps

To use this feature, please enable the PimcoreSeoBundle in your bundle.php file and install it accordingly with the following command:

- [Sitemaps](/Pimcore/Tools_and_Features/Sitemaps.md): To use this feature, please enable the PimcoreSeoBundle in your bundle.php file and install it accordingly with the following command:

##### System_Settings

In the system settings (Settings > System Settings) system-wide settings for Pimcore can be made. Changes should

- [System Settings](/Pimcore/Tools_and_Features/System_Settings.md): In the system settings (Settings > System Settings) system-wide settings for Pimcore can be made. Changes should

##### Tags

General

- [Tags](/Pimcore/Tools_and_Features/Tags.md): General

##### Two_Factor_Authentication

since build 256

- [Two Factor Authentication](/Pimcore/Tools_and_Features/Two_Factor_Authentication.md): since build 256

##### Versioning

General

- [Versioning](/Pimcore/Tools_and_Features/Versioning.md): General

##### Website_Settings

The Website Settings give you the possibility to configure website-specific settings, which you can

- [Website Settings](/Pimcore/Tools_and_Features/Website_Settings.md): The Website Settings give you the possibility to configure website-specific settings, which you can

#### Workflow_Management

General

- [Workflow Management](/Pimcore/Workflow_Management.md): General

##### Configuration_Details

Available options

- [Details](/Pimcore/Workflow_Management/Configuration_Details.md): Available options
- [Placeholder Example](/Pimcore/Workflow_Management/Configuration_Details/Placeholder_Example.md): Workflows support placeholders. By using placeholders, it is possible, to reuse an existing workflow and reapply places and transitions onto a new workflow.

##### Marking_Stores

The Pimcore workflow engine provides several different ways how to store the actual places of a subject. These are

- [Marking Stores](/Pimcore/Workflow_Management/Marking_Stores.md): The Pimcore workflow engine provides several different ways how to store the actual places of a subject. These are

##### Permissions

As listed in the configuration details section it's possible to modify the Pimcore

- [Modifying Pimcore Permissions Based On Workflow Places](/Pimcore/Workflow_Management/Permissions.md): As listed in the configuration details section it's possible to modify the Pimcore

##### Support_Strategies

The workflow engine offers several different ways to define which entities are supported by the configured workflow.

- [Support Strategies](/Pimcore/Workflow_Management/Support_Strategies.md): The workflow engine offers several different ways to define which entities are supported by the configured workflow.

##### Workflow_Report

Depending on the marking store of workflows, different ways of Workflow Reporting are available. For details see

- [Workflow Reporting](/Pimcore/Workflow_Management/Workflow_Report.md): Depending on the marking store of workflows, different ways of Workflow Reporting are available. For details see

##### Workflow_Tutorial

Let's make a one truly simple example workflow for product objects.

- [Simple Workflow Tutorial](/Pimcore/Workflow_Management/Workflow_Tutorial.md): Let's make a one truly simple example workflow for product objects.

##### Working_with_PHP_API

The Pimcore workflow management can also be used via PHP API.

- [Working With PHP API](/Pimcore/Workflow_Management/Working_with_PHP_API.md): The Pimcore workflow management can also be used via PHP API.

### Platform_Version

The Pimcore platform consists of the Pimcore Core Framework and Core Extensions that can be added for your needs.

- [Pimcore Platform Version](/Platform_Version.md): The Pimcore platform consists of the Pimcore Core Framework and Core Extensions that can be added for your needs.

#### Platform_Version_Releases

Following platform version releases are available.

- [Platform Version Releases and Support](/Platform_Version/Platform_Version_Releases.md): Following platform version releases are available.

##### 2022.0

Following table lists all Pimcore modules and their version included in platform release 2022.0:

- [2022.0 (LTS)](/Platform_Version/Platform_Version_Releases/2022.0.md): Following table lists all Pimcore modules and their version included in platform release 2022.0:

##### 2023.1

Following table lists all Pimcore modules and their version included in platform release 2023.1:

- [2023.1](/Platform_Version/Platform_Version_Releases/2023.1.md): Following table lists all Pimcore modules and their version included in platform release 2023.1:

##### 2023.2

Following table lists all Pimcore modules and their version included in platform release 2023.2:

- [2023.2](/Platform_Version/Platform_Version_Releases/2023.2.md): Following table lists all Pimcore modules and their version included in platform release 2023.2:

##### 2023.3

Following table lists all Pimcore modules and their version included in platform release 2023.3:

- [2023.3 (LTS)](/Platform_Version/Platform_Version_Releases/2023.3.md): Following table lists all Pimcore modules and their version included in platform release 2023.3:

##### 2024.1

Following table lists all Pimcore modules and their version included in platform release 2024.1:

- [2024.1](/Platform_Version/Platform_Version_Releases/2024.1.md): Following table lists all Pimcore modules and their version included in platform release 2024.1:

##### 2024.2

Following table lists all Pimcore modules and their version included in platform release 2024.2:

- [2024.2](/Platform_Version/Platform_Version_Releases/2024.2.md): Following table lists all Pimcore modules and their version included in platform release 2024.2:

##### 2024.3

Following table lists all Pimcore modules and their version included in platform release 2024.3:

- [2024.3](/Platform_Version/Platform_Version_Releases/2024.3.md): Following table lists all Pimcore modules and their version included in platform release 2024.3:

##### 2024.4

Following table lists all Pimcore modules and their version included in platform release 2024.4:

- [2024.4 (LTS)](/Platform_Version/Platform_Version_Releases/2024.4.md): Following table lists all Pimcore modules and their version included in platform release 2024.4:

##### 2025.1

Following table lists all Pimcore modules and their version included in platform release 2025.1:

- [2025.1](/Platform_Version/Platform_Version_Releases/2025.1.md): Following table lists all Pimcore modules and their version included in platform release 2025.1:

##### 2025.2

Following table lists all Pimcore modules and their version included in platform release 2025.2:

- [2025.2](/Platform_Version/Platform_Version_Releases/2025.2.md): Following table lists all Pimcore modules and their version included in platform release 2025.2:

##### 2025.3

Following table lists all Pimcore modules and their version included in platform release 2025.3:

- [2025.3](/Platform_Version/Platform_Version_Releases/2025.3.md): Following table lists all Pimcore modules and their version included in platform release 2025.3:

#### Release_Notes

Following list includes all available Platform Version release notes:

- [Release Notes](/Platform_Version/Release_Notes.md): Following list includes all available Platform Version release notes:

##### 2023.1

Here you will find all the important new features and release notes of the Platform Version 2023.1 release.

- [Release 2023.1](/Platform_Version/Release_Notes/2023.1.md): Here you will find all the important new features and release notes of the Platform Version 2023.1 release.

##### 2023.2

Here you will find all the important new features and release notes of the Platform Version 2023.2 release.

- [Release 2023.2](/Platform_Version/Release_Notes/2023.2.md): Here you will find all the important new features and release notes of the Platform Version 2023.2 release.

##### 2023.3

Here you will find all the important new features and release notes of the Platform Version 2023.3 release.

- [Release 2023.3](/Platform_Version/Release_Notes/2023.3.md): Here you will find all the important new features and release notes of the Platform Version 2023.3 release.

##### 2024.1

Here you will find all the important new features and release notes of the Platform Version 2024.1 release.

- [Release 2024.1](/Platform_Version/Release_Notes/2024.1.md): Here you will find all the important new features and release notes of the Platform Version 2024.1 release.

##### 2024.2

Here you will find all the important new features and release notes of the Platform Version 2024.2 release.

- [Release 2024.2](/Platform_Version/Release_Notes/2024.2.md): Here you will find all the important new features and release notes of the Platform Version 2024.2 release.

##### 2024.3

Here you will find all the important new features and release notes of the Platform Version 2024.3 release.

- [Release 2024.3](/Platform_Version/Release_Notes/2024.3.md): Here you will find all the important new features and release notes of the Platform Version 2024.3 release.

##### 2024.4

Here you will find all the important new features and release notes of the Platform Version 2024.4 release.

- [Release 2024.4](/Platform_Version/Release_Notes/2024.4.md): Here you will find all the important new features and release notes of the Platform Version 2024.4 release.

##### 2025.1

Here you will find all the important new features and release notes of the Platform Version 2025.1 release.

- [Release 2025.1](/Platform_Version/Release_Notes/2025.1.md): Here you will find all the important new features and release notes of the Platform Version 2025.1 release.

##### 2025.2

Here you will find all the important new features and release notes of the Platform Version 2025.2 release.

- [Release 2025.2](/Platform_Version/Release_Notes/2025.2.md): Here you will find all the important new features and release notes of the Platform Version 2025.2 release.

##### 2025.3

Here you will find all the important new features and release notes of the Platform Version 2025.3 release.

- [Release 2025.3](/Platform_Version/Release_Notes/2025.3.md): Here you will find all the important new features and release notes of the Platform Version 2025.3 release.

#### Setup

On technical level, the Platform Version comes as a composer dependency and can be added to the projects composer.json.

- [Setup and working with Platform Versions](/Platform_Version/Setup.md): On technical level, the Platform Version comes as a composer dependency and can be added to the projects composer.json.

### Portal_Engine

Pimcore Portal Engine allows creating outstanding Asset Experience Portals and Product Experience Portals just by configuration - no coding needed.

- [Pimcore Portal Engine](/Portal_Engine.md): Pimcore Portal Engine allows creating outstanding Asset Experience Portals and Product Experience Portals just by configuration - no coding needed.

#### Administration_of_Portals

Experience portals powered by the portal engine are pretty much standard Pimcore applications. The configuration

- [Administration of Portals](/Portal_Engine/Administration_of_Portals.md): Experience portals powered by the portal engine are pretty much standard Pimcore applications. The configuration

##### Background_Processes

Processes started via Portal Engine Frontend

- [Background Processes](/Portal_Engine/Administration_of_Portals/Background_Processes.md): Processes started via Portal Engine Frontend

##### Configuration

The configuration of the portals takes place in Pimcore documents. Each portal is defined as a Pimcore site and

- [Configuration](/Portal_Engine/Administration_of_Portals/Configuration.md): The configuration of the portals takes place in Pimcore documents. Each portal is defined as a Pimcore site and
- [Dashboard and Content Pages](/Portal_Engine/Administration_of_Portals/Configuration/Dashboard_and_Content_Pages.md): Content pages as well as the dashboard page are standard Pimcore documents. Their content structure and content
- [Asset Data Pool](/Portal_Engine/Administration_of_Portals/Configuration/Data_Pool_Configurations/Asset_Data_Pools.md): The asset data pool allows integrating Pimcore assets into a portal. Configurations are available for the grid
- [Data Object Data Pool](/Portal_Engine/Administration_of_Portals/Configuration/Data_Pool_Configurations/Object_Data_Pools.md): The data object data pool allows integrating Pimcore data objects into a portal. Configurations are available for
- [Multi Language Portals](/Portal_Engine/Administration_of_Portals/Configuration/Multi_Language_Portals.md): Multi language portals are supported via language variant document trees. The easiest way setting up a multi language is
- [Portal Setup Wizard](/Portal_Engine/Administration_of_Portals/Configuration/Portal_Setup_Wizard.md): The portal engine ships with a wizard to support with initial portal setup. It helps to create the initial document
- [Styling Settings and Frontend Build](/Portal_Engine/Administration_of_Portals/Configuration/Styling_Settings_and_Frontend_Build.md): It is possible to customize the styling of portals and thus change their look and feel.
- [User Management](/Portal_Engine/Administration_of_Portals/Configuration/User_Management.md): Users and their permissions are managed via Pimcore data objects of type PortalUser. Each user has a couple
- [Guest User](/Portal_Engine/Administration_of_Portals/Configuration/User_Management/Guest_User.md): It is possible, to activate guest user login for accessing the portal.
- [OpenID Connect Integration](/Portal_Engine/Administration_of_Portals/Configuration/User_Management/OpenID_Connect_Integration.md): For OpenID Connect Integration, the Pimcore OpenID Connect bundle needs to be added via composer and enabled.

##### Direct_Edit

To make Pimcore Direct Edit work in the portals, following things need to be considered.

- [Direct Edit](/Portal_Engine/Administration_of_Portals/Direct_Edit.md): To make Pimcore Direct Edit work in the portals, following things need to be considered.

##### House_Keeping

For certain processes within the portal engine it is needed to store temporary files in the file system. The portal

- [House Keeping](/Portal_Engine/Administration_of_Portals/House_Keeping.md): For certain processes within the portal engine it is needed to store temporary files in the file system. The portal

##### Statistics

The portal engine integrates the Pimcore Statistics Explorer to get

- [Statistics](/Portal_Engine/Administration_of_Portals/Statistics.md): The portal engine integrates the Pimcore Statistics Explorer to get

#### Development_Documentation

The portal engine offers quite a lot of configuration possibilities through its administration features.

- [Development Documentation](/Portal_Engine/Development_Documentation.md): The portal engine offers quite a lot of configuration possibilities through its administration features.

##### Customize_and_Extend_Behavior

In this chapter you will find more information about how to customize

- [Customize and Extend Behavior](/Portal_Engine/Development_Documentation/Customize_and_Extend_Behavior.md): In this chapter you will find more information about how to customize
- [Add additional Area Bricks](/Portal_Engine/Development_Documentation/Customize_and_Extend_Behavior/Add_Area_Bricks.md): Area bricks are principally implemented the same way as described in the
- [Data Exporter](/Portal_Engine/Development_Documentation/Customize_and_Extend_Behavior/Data_Exporter.md): The Portal Engine ships with a service which can make structured data of data objects and assets downloadable via the
- [Events](/Portal_Engine/Development_Documentation/Customize_and_Extend_Behavior/Events.md): Within the portal engine the following events are triggered to allow influencing certain behaviors. All of these events
- [External Authentication Services](/Portal_Engine/Development_Documentation/Customize_and_Extend_Behavior/External_Authentication_Service.md): The portal engine provides several events to make it possible to integrate
- [Main Image Extractor for Data Objects](/Portal_Engine/Development_Documentation/Customize_and_Extend_Behavior/Main_Image_Extractor.md): The main image extractor is used to extract the main image of a data object. The main image will be visible for example
- [Name Extractor for Pimcore Elements](/Portal_Engine/Development_Documentation/Customize_and_Extend_Behavior/Name_Extractor.md): The name extractor is used to extract a name for data objects and assets. This name will be displayed and used at the
- [Precondition Service for Data Pools](/Portal_Engine/Development_Documentation/Customize_and_Extend_Behavior/PreCondition_Service.md): Data pool workspaces and permissions provide a good way to configure which assets and data objects should be visible in

##### Customize_Appearance

The appearance of the portal engine can be customized in multiple different ways. The simplest way is to customize the

- [Customize Appearance](/Portal_Engine/Development_Documentation/Customize_Appearance.md): The appearance of the portal engine can be customized in multiple different ways. The simplest way is to customize the
- [Customize Frontend Build](/Portal_Engine/Development_Documentation/Customize_Appearance/Customize_Frontend_Build.md): Most of the frontend parts of the portal engine are built with client side technologies. The JavaScript components are
- [Frontend folder structure](/Portal_Engine/Development_Documentation/Customize_Appearance/Frontend_Architecture.md): The following chapter contains a deep dive into the folder structure of the frontend application and some important
- [JSX Components](/Portal_Engine/Development_Documentation/Customize_Appearance/JSX_Components.md): The portal engine is build with customization in mind and offers a very flexible way to customized the application.
- [Overwrite Templates](/Portal_Engine/Development_Documentation/Customize_Appearance/Overwrite_Templates.md): React components

##### Search_Index_Management

- [Extending Search Index](/Portal_Engine/Development_Documentation/Search_Index_Management/Extend_Search_Index.md): The portal engine is powered by OpenSearch via Generic Data Index bundle for search, listings and filters. Therefore, it's needed to store the data

#### Installation

The installation of the Portal Engine follows standard composer procedures.

- [Installation of Portal Engine](/Portal_Engine/Installation.md): The installation of the Portal Engine follows standard composer procedures.

##### Upgrade

Following steps are necessary during updating to newer versions.

- [Upgrade Information](/Portal_Engine/Installation/Upgrade.md): Following steps are necessary during updating to newer versions.

#### User_Documentation_for_Portals

Portals can be Asset Experience Portals or Product Experience Portals (or both) and deliver any data that is

- [User Documentation](/Portal_Engine/User_Documentation_for_Portals.md): Portals can be Asset Experience Portals or Product Experience Portals (or both) and deliver any data that is

##### Asset_Features

- [Asset Detail Page](/Portal_Engine/User_Documentation_for_Portals/Asset_Features/Asset_Detail.md): Asset detail pages are used for all asset data pools in the portals. The actual available
- [Asset Grid](/Portal_Engine/User_Documentation_for_Portals/Asset_Features/Asset_Grid.md): Asset grids are used for all asset data pools in the portals. The actual available
- [Assets Upload Dialog](/Portal_Engine/User_Documentation_for_Portals/Asset_Features/Asset_Upload_Dialog.md): The upload dialog provides a multi-step wizard to upload new assets and enrich them
- [Upload Folder](/Portal_Engine/User_Documentation_for_Portals/Asset_Features/Upload_Folder.md): The dedicated upload folder is a special folder outside the data pool workspace that

##### Data_Object_Features

- [Data Object Detail Page](/Portal_Engine/User_Documentation_for_Portals/Data_Object_Features/Data_Object_Detail.md): Data object detail pages are used for all data object data pools in the portals. The actual available
- [Data Object Grid](/Portal_Engine/User_Documentation_for_Portals/Data_Object_Features/Data_Object_Grid.md): Data Object grids are used for all data object data pools in the portals. The actual available

##### General_Features

- [Background Tasks and Notifications](/Portal_Engine/User_Documentation_for_Portals/General_Features/Background_Tasks.md): Various actions in the portals like batch updating, batch downloading etc. result in long-running processes. The portal
- [Collections](/Portal_Engine/User_Documentation_for_Portals/General_Features/Collections.md): Collections allow collecting data that belongs somehow together into one list. They can hold elements from different
- [Collections in Pimcore Backend](/Portal_Engine/User_Documentation_for_Portals/General_Features/Collections/Collections_in_Pimcore_Backend.md): Collections are also accessible in Pimcore backend. Thus, it is possible to manage collections and use collections
- [Collections in Portal Frontends](/Portal_Engine/User_Documentation_for_Portals/General_Features/Collections/Collections_in_Portals.md): Portal frontends fully integrate collections and thus they are an essential part of every portal.
- [Dashboard and Content Pages](/Portal_Engine/User_Documentation_for_Portals/General_Features/Dashboard_and_Content_Pages.md): Dashboard
- [Download Cart](/Portal_Engine/User_Documentation_for_Portals/General_Features/Download_Cart.md): The download cart allows collecting multiple items of different data pools and download them together in one zip file.
- [Login](/Portal_Engine/User_Documentation_for_Portals/General_Features/Login.md): Before using any portal created with the portal engine, users have to login into the system.
- [Multi Language Portals](/Portal_Engine/User_Documentation_for_Portals/General_Features/Multi_Language_Portals.md): In terms of multi language there are two aspects to be considered:
- [Multi Selection](/Portal_Engine/User_Documentation_for_Portals/General_Features/Multi_Selection.md): All listings of data pools support multi selection.
- [Public Sharing](/Portal_Engine/User_Documentation_for_Portals/General_Features/Public_Sharing.md): The public sharing feature allows sharing one or multiple elements with users that don't have a login for the portal.
- [Reporting and Statistics](/Portal_Engine/User_Documentation_for_Portals/General_Features/Reporting_and_Statistics.md): Predefined Statistics
- [Search](/Portal_Engine/User_Documentation_for_Portals/General_Features/Search.md): Each portal has a powerful fulltext search powered by OpenSearch via Generic Data Index Bundle. It searches for given search terms in all
- [Structure and Navigation](/Portal_Engine/User_Documentation_for_Portals/General_Features/Structure_and_Navigation.md): Header
- [Users and Profile](/Portal_Engine/User_Documentation_for_Portals/General_Features/Users_and_Profile.md): Profile Page

### Quill_WYSWIYG_Editor

This bundle provides the Quill 2.x WYSIWYG editor integration for Pimcore.

- [Quill WYSIWYG Pimcore Bundle](/Quill_WYSWIYG_Editor.md): This bundle provides the Quill 2.x WYSIWYG editor integration for Pimcore.

#### Global_Configuration_Admin_Ui

Global Configuration (only for admin-ui-classic-bundle)

- [Global_Configuration_Admin_Ui](/Quill_WYSWIYG_Editor/Global_Configuration_Admin_Ui.md): Global Configuration (only for admin-ui-classic-bundle)

#### Global_Configuration_Studio_Ui

Global Configuration (only for studio-ui-bundle)

- [Global_Configuration_Studio_Ui](/Quill_WYSWIYG_Editor/Global_Configuration_Studio_Ui.md): Global Configuration (only for studio-ui-bundle)

#### Installation

Make sure the bundle is enabled in the config/bundles.php file. The following lines should be added:

- [Installation](/Quill_WYSWIYG_Editor/Installation.md): Make sure the bundle is enabled in the config/bundles.php file. The following lines should be added:

#### Migration_to_Quill

Every WYSIWYG-Editor (TinyMCE, CKEditor, ...) has its own peculiarities, that means that they are never 100% compatible to each other. Potential incompatibilities can result into different markup or styling, in rare edge-cases even in a kind of data-loss if the existing markup is not supported by Quill. Therefore it's important to check your existing contents for compatibility with the editor.

- [Migration to Quill](/Quill_WYSWIYG_Editor/Migration_to_Quill.md): Every WYSIWYG-Editor (TinyMCE, CKEditor, ...) has its own peculiarities, that means that they are never 100% compatible to each other. Potential incompatibilities can result into different markup or styling, in rare edge-cases even in a kind of data-loss if the existing markup is not supported by Quill. Therefore it's important to check your existing contents for compatibility with the editor.

### Statistics_Explorer

Pimcore Statistics Explorer provides a simple statistics tool that can be integrated into your application allowing users to dig into data and create statistic reports.

- [Pimcore Statistics Explorer](/Statistics_Explorer.md): Pimcore Statistics Explorer provides a simple statistics tool that can be integrated into your application allowing users to dig into data and create statistic reports.

#### Application_Integration

Statistics Explorer provides a UI for configuring and displaying reports. To use it, it must be embedded into an

- [Application Integration](/Statistics_Explorer/Application_Integration.md): Statistics Explorer provides a UI for configuring and displaying reports. To use it, it must be embedded into an

##### Configuration

The statistics explorer can be executed in multiple contexts within one host application and each

- [Configuration](/Statistics_Explorer/Application_Integration/Configuration.md): The statistics explorer can be executed in multiple contexts within one host application and each
- [Elasticsearch configuration](/Statistics_Explorer/Application_Integration/Configuration/Elasticsearch_Configuration.md): The ElasticsearchAdapter
- [Opensearch configuration](/Statistics_Explorer/Application_Integration/Configuration/Opensearch_Configuration.md): The OpensearchAdapter

##### Embedding_Statistics_Explorer

Steps to Embed into Application

- [Embedding Statistics Explorer](/Statistics_Explorer/Application_Integration/Embedding_Statistics_Explorer.md): Steps to Embed into Application

##### Embedding_Statistics_Loader

The statistics loader can be used to embed the report (table and chart) of stored

- [Using Statistics Loader](/Statistics_Explorer/Application_Integration/Embedding_Statistics_Loader.md): The statistics loader can be used to embed the report (table and chart) of stored

#### Basic_Concepts

Pimcore Statistics Explorer is a lightweight statistics tool designed for easy integration into applications.

- [Basic Concepts](/Statistics_Explorer/Basic_Concepts.md): Pimcore Statistics Explorer is a lightweight statistics tool designed for easy integration into applications.

##### List_Mode

The List Mode allows creating simple, structured listings with filtering and sorting. It is ideal for retrieving

- [List Mode](/Statistics_Explorer/Basic_Concepts/List_Mode.md): The List Mode allows creating simple, structured listings with filtering and sorting. It is ideal for retrieving

##### Statistics_Mode

The Statistics Mode enables the creation of aggregated reports, calculating metrics, and generating pivot tables.

- [Statistics Mode](/Statistics_Explorer/Basic_Concepts/Statistics_Mode.md): The Statistics Mode enables the creation of aggregated reports, calculating metrics, and generating pivot tables.

#### Contribution_Guide

Frontend Builds

- [Contribution Guide](/Statistics_Explorer/Contribution_Guide.md): Frontend Builds

#### Custom_Report_Integration

Overview

- [Custom Report Integration](/Statistics_Explorer/Custom_Report_Integration.md): Overview

##### List_Mode_Adapter

The List Mode Adapter enables generating reports in List Mode within

- [List Mode Adapter](/Statistics_Explorer/Custom_Report_Integration/List_Mode_Adapter.md): The List Mode Adapter enables generating reports in List Mode within

##### Statistics_Mode_Adapter

The Statistics Mode Adapter enables generating reports in Statistics Mode

- [Statistics Mode Adapter](/Statistics_Explorer/Custom_Report_Integration/Statistics_Mode_Adapter.md): The Statistics Mode Adapter enables generating reports in Statistics Mode

#### Customizing

Statistics Explorer provides several customization options to adapt its functionality to specific requirements.

- [Customizing](/Statistics_Explorer/Customizing.md): Statistics Explorer provides several customization options to adapt its functionality to specific requirements.

##### Adding_Data_Sources_during_Runtime

It is possible to add additional data sources during runtime by listening to following event.

- [Adding Data Sources during Runtime](/Statistics_Explorer/Customizing/Adding_Data_Sources_during_Runtime.md): It is possible to add additional data sources during runtime by listening to following event.

##### Customizing_Results

Depending on context and use case it might be necessary to customize the result before

- [Customizing Results](/Statistics_Explorer/Customizing/Customizing_Results.md): Depending on context and use case it might be necessary to customize the result before

##### Providing_Predefined_Statistic_Configuration

There are two options to provide predefined statistic configurations that are globally visible

- [Provide Predefined Statistic Configuration](/Statistics_Explorer/Customizing/Providing_Predefined_Statistic_Configuration.md): There are two options to provide predefined statistic configurations that are globally visible

#### Installation_and_Configuration

Bundle Installation

- [Installation and Configuration](/Statistics_Explorer/Installation_and_Configuration.md): Bundle Installation

##### Upgrade

Following steps are necessary during updating to newer versions.

- [Upgrade Information](/Statistics_Explorer/Installation_and_Configuration/Upgrade.md): Following steps are necessary during updating to newer versions.

#### Tracking_Events

The bundle also ships with basis to easily implement a tracker that tracks events

- [Tracking Events](/Statistics_Explorer/Tracking_Events.md): The bundle also ships with basis to easily implement a tracker that tracks events

### Studio_Backend

The Pimcore Studio Backend serves as the central hub for API endpoints and RPC calls.

- [Pimcore Studio Backend](/Studio_Backend.md): The Pimcore Studio Backend serves as the central hub for API endpoints and RPC calls.

#### Additional_Custom_Attributes

Pimcore Studio Backend allows you to add additional data to response schemas.

- [Additional and Custom Attributes](/Studio_Backend/Additional_Custom_Attributes.md): Pimcore Studio Backend allows you to add additional data to response schemas.

#### Api_Testing

Set up local test environment

- [Api Testing](/Studio_Backend/Api_Testing.md): Set up local test environment

#### Extending_Studio

Pimcore Studio Backend Bundle can be extended to add custom endpoints, filters, grid customizations a.m.m.

- [Extending Pimcore Studio Backend](/Studio_Backend/Extending_Studio.md): Pimcore Studio Backend Bundle can be extended to add custom endpoints, filters, grid customizations a.m.m.

##### Assets

- [Extending metadata adapters](/Studio_Backend/Extending_Studio/Assets/Metadata_adapters.md): Asset metadata adapters are used to process metadata values before they are, e.g., saved to the database or displayed in the user interface.

##### Data_Objects

- [Field Definition Adapters](/Studio_Backend/Extending_Studio/Data_Objects/Field_Definition_adapters.md): TBA

##### Documents

- [Custom document types](/Studio_Backend/Extending_Studio/Documents/Document_types.md): There are by default six document types in Pimcore Studio:

##### Dot_Notation

Studio provides a Dot Notation to resolve the field defintion of an object.

- [Dot Notation for Field Definitions](/Studio_Backend/Extending_Studio/Dot_Notation.md): Studio provides a Dot Notation to resolve the field defintion of an object.

##### Endpoints

Endpoints can be added at any given point. In order to show up in the OpenApi documentation you need to add the according OpenApi attributes.

- [Extending Endpoints](/Studio_Backend/Extending_Studio/Endpoints.md): Endpoints can be added at any given point. In order to show up in the OpenApi documentation you need to add the according OpenApi attributes.

##### Events

The Pimcore Studio Backend provides a lot of events to hook into the system and extend the functionality. The events are dispatched via the Symfony event dispatcher and can be used to add custom logic to the system.

- [Extending via Events](/Studio_Backend/Extending_Studio/Events.md): The Pimcore Studio Backend provides a lot of events to hook into the system and extend the functionality. The events are dispatched via the Symfony event dispatcher and can be used to add custom logic to the system.

##### Filters

Currently, there are two different filters systems implemented. The Search Index Filters and the Listing Filters.

- [Extending Filters](/Studio_Backend/Extending_Studio/Filters.md): Currently, there are two different filters systems implemented. The Search Index Filters and the Listing Filters.
- [Extending Listing Filters](/Studio_Backend/Extending_Studio/Filters/Listing_Filters.md): The Listing Filters are based on the Pimcore Listing classes and provide an abstraction layer to add filters to a listing.
- [Extending Search Index Filters](/Studio_Backend/Extending_Studio/Filters/Search_Index_Filters.md): The Filters are based on the Generic Data Index Bundle and provide an abstraction layer to add filters to an OpenSearch or Elasticsearch query.

##### Gdpr

The GDPR Data Provider system provides a centralized interface to find and export personal data from any part of your Pimcore application. You can add new data sources (like Data Objects, Assets, Users, or any custom entity) by creating your own provider.

- [Extending GDPR Data Providers](/Studio_Backend/Extending_Studio/Gdpr.md): The GDPR Data Provider system provides a centralized interface to find and export personal data from any part of your Pimcore application. You can add new data sources (like Data Objects, Assets, Users, or any custom entity) by creating your own provider.

##### Grid

The grid in Studio is based on Column Definitions that define how the columns are represented, column resolvers that define how the data is obtained from the object and column collectors that show what type of columns are available.

- [Extending Grid with Custom Columns](/Studio_Backend/Extending_Studio/Grid.md): The grid in Studio is based on Column Definitions that define how the columns are represented, column resolvers that define how the data is obtained from the object and column collectors that show what type of columns are available.

##### Notes

Notes to log changes or events on elements independently of the versioning. You can get more general information about notes here

- [Extending Notes](/Studio_Backend/Extending_Studio/Notes.md): Notes to log changes or events on elements independently of the versioning. You can get more general information about notes here

##### OpenApi

For the OpenApi documentation we are using the zircote/swagger-php library.

- [Extending OpenApi](/Studio_Backend/Extending_Studio/OpenApi.md): For the OpenApi documentation we are using the zircote/swagger-php library.

##### Perspectives

Perspectives allow to create different views in the backend UI and even customize the standard perspective. They are using widgets to create unique views for the Pimcore Studio.

- [Extending Perspectives](/Studio_Backend/Extending_Studio/Perspectives.md): Perspectives allow to create different views in the backend UI and even customize the standard perspective. They are using widgets to create unique views for the Pimcore Studio.
- [Extending Widgets](/Studio_Backend/Extending_Studio/Perspectives/Widgets.md): Widgets are the main building blocks of the perspectives. They can be used to create unique views for the Pimcore Studio.

##### Update_Patch

Updating and patching elements should be done via the update and patch endpoints.

- [Extending Updater and Patcher](/Studio_Backend/Extending_Studio/Update_Patch.md): Updating and patching elements should be done via the update and patch endpoints.

#### Generic_Execution_Engine

This documentation is currently work in progress and will be updated soon.

- [Generic Execution Engine](/Studio_Backend/Generic_Execution_Engine.md): This documentation is currently work in progress and will be updated soon.

#### Grid

On the request level we have three main components for the grid: Column, ColumnConfiguration and ColumnData.

- [Grid](/Studio_Backend/Grid.md): On the request level we have three main components for the grid: Column, ColumnConfiguration and ColumnData.

#### Installation

Bundle Installation

- [Installation of the Studio Backend Bundle](/Studio_Backend/Installation.md): Bundle Installation

#### Mercure_Setup

Start and configure Mercure server

- [Mercure Setup](/Studio_Backend/Mercure_Setup.md): Start and configure Mercure server

#### User

Default Key Bindings

- [Studio User](/Studio_Backend/User.md): Default Key Bindings

### Studio_UI

The Studio UI Bundle provides a Backend UI for Pimcore. The Studio UI relies on the Studio Backend Bundle, which provides the backend API for the Studio UI.

- [Studio UI](/Studio_UI.md): The Studio UI Bundle provides a Backend UI for Pimcore. The Studio UI relies on the Studio Backend Bundle, which provides the backend API for the Studio UI.

#### Configuration

The Studio UI Bundle offers several configuration options, which are listed below.

- [Configuration for the Studio UI Bundle](/Studio_UI/Configuration.md): The Studio UI Bundle offers several configuration options, which are listed below.

##### Additional_Static_Resources

In general, Studio UI plugins require a Webpack build and entry point files to function correctly. For more details, refer to the Getting started with your first plugin guide.

- [Including Additional CSS or JS Files](/Studio_UI/Configuration/Additional_Static_Resources.md): In general, Studio UI plugins require a Webpack build and entry point files to function correctly. For more details, refer to the Getting started with your first plugin guide.

##### Content_Security_Policy

Overview

- [Content Security Policy](/Studio_UI/Configuration/Content_Security_Policy.md): Overview

##### Custom_URL_for_the_UI

To change the URL you can add the following configuration:

- [Custom URL for the UI](/Studio_UI/Configuration/Custom_URL_for_the_UI.md): To change the URL you can add the following configuration:

##### Wysiwyg

You can define a global configuration for all WYSIWYG editors used in data objects and documents. Below is an example configuration:

- [Global Configuration](/Studio_UI/Configuration/Wysiwyg.md): You can define a global configuration for all WYSIWYG editors used in data objects and documents. Below is an example configuration:

#### Examples

This section contains various examples to help you get started with plugin development for Pimcore Studio. Each example demonstrates a specific feature or functionality that you can implement in your plugins:

- [Plugin Development Examples](/Studio_UI/Examples.md): This section contains various examples to help you get started with plugin development for Pimcore Studio. Each example demonstrates a specific feature or functionality that you can implement in your plugins:

##### Add_a_additional_asset_editor_toolbar_button

Overview

- [How to Add an Additional Button to the Asset Editor Toolbar](/Studio_UI/Examples/Add_a_additional_asset_editor_toolbar_button.md): Overview

##### Add_a_main_nav_entry

Overview

- [How to Add a Main Navigation Entry](/Studio_UI/Examples/Add_a_main_nav_entry.md): Overview

##### Add_an_entry_to_left_sidebar

Overview

- [How to Add an Entry to the Left Sidebar](/Studio_UI/Examples/Add_an_entry_to_left_sidebar.md): Overview

##### Adding_custom_icons

Overview

- [How to Add Custom Icons](/Studio_UI/Examples/Adding_custom_icons.md): Overview

##### Custom_Listing

Overview

- [How to Create a Custom Listing](/Studio_UI/Examples/Custom_Listing.md): Overview

##### Customize_Context_Menus

Overview

- [How to Customize Context Menus](/Studio_UI/Examples/Customize_Context_Menus.md): Overview

##### Use_Api_data

Overview

- [How to Use Api Data](/Studio_UI/Examples/Use_Api_data.md): Overview

##### Use_Dynamic_Types

Overview

- [How to Use Dynamic Types](/Studio_UI/Examples/Use_Dynamic_Types.md): Overview

##### Use_the_Tab_Manager

Overview

- [How to Use the Tab Manager](/Studio_UI/Examples/Use_the_Tab_Manager.md): Overview

##### Use_the_Widget_Manager

Overview

- [How to use the widget manager](/Studio_UI/Examples/Use_the_Widget_Manager.md): Overview

#### Installation

Bundle Installation

- [Installation of the Studio Ui Bundle](/Studio_UI/Installation.md): Bundle Installation

#### PimcoreStudio_Window_API

The PimcoreStudio Window API provides a communication bridge between iframe-based components and the parent Studio UI window. This API enables seamless interaction and data exchange in Pimcore's multi-frame architecture.

- [PimcoreStudio Window API](/Studio_UI/PimcoreStudio_Window_API.md): The PimcoreStudio Window API provides a communication bridge between iframe-based components and the parent Studio UI window. This API enables seamless interaction and data exchange in Pimcore's multi-frame architecture.

#### Plugins_Getting_Started

The Pimcore Studio plugin system allows developers to extend the functionality of Pimcore Studio by creating custom plugins.

- [Getting Started With Your First Plugin](/Studio_UI/Plugins_Getting_Started.md): The Pimcore Studio plugin system allows developers to extend the functionality of Pimcore Studio by creating custom plugins.

#### SDK_Overview

The Pimcore Studio UI SDK empowers developers to extend and customize specific features seamlessly.

- [SDK Overview](/Studio_UI/SDK_Overview.md): The Pimcore Studio UI SDK empowers developers to extend and customize specific features seamlessly.

##### Component_Registry

The Component Registry is a centralized system for managing React UI components in Pimcore Studio UI. It defines which components are rendered in specific contexts or for particular purposes. This allows developers to exchange certain components in plugins or dynamically register additional items in slots, enabling seamless customization and extension of the UI.

- [Component Registry](/Studio_UI/SDK_Overview/Component_Registry.md): The Component Registry is a centralized system for managing React UI components in Pimcore Studio UI. It defines which components are rendered in specific contexts or for particular purposes. This allows developers to exchange certain components in plugins or dynamically register additional items in slots, enabling seamless customization and extension of the UI.

##### Context_Menu_Registry

The Context Menu Registry is a centralized system for managing context menu items in Pimcore Studio UI. It enables developers to register custom menu items for different contexts such as tree nodes, grid rows, and editor toolbars.

- [Context Menu Registry](/Studio_UI/SDK_Overview/Context_Menu_Registry.md): The Context Menu Registry is a centralized system for managing context menu items in Pimcore Studio UI. It enables developers to register custom menu items for different contexts such as tree nodes, grid rows, and editor toolbars.

##### Dynamic_Types

Introduction

- [Dynamic Types](/Studio_UI/SDK_Overview/Dynamic_Types.md): Introduction

##### Plugin_Architecture_Plugins_Modules

Plugin Architecture

- [Plugin Architecture, Plugins, and Modules](/Studio_UI/SDK_Overview/Plugin_Architecture_Plugins_Modules.md): Plugin Architecture

##### RTK_Query_API

The RTK Query API (slices) is automatically generated via @rtk-query/codegen-openapi from our OpenAPI specification. This ensures that the API definitions, including schemas and types, are always consistent with the backend. By automating this process, we reduce manual effort, minimize errors, and ensure that the API is always up-to-date with backend changes.

- [RTK Query API](/Studio_UI/SDK_Overview/RTK_Query_API.md): The RTK Query API (slices) is automatically generated via @rtk-query/codegen-openapi from our OpenAPI specification. This ensures that the API definitions, including schemas and types, are always consistent with the backend. By automating this process, we reduce manual effort, minimize errors, and ensure that the API is always up-to-date with backend changes.

##### SDK_Imports

The Pimcore Studio UI SDK provides a set of predefined imports that developers can use to build and extend functionalities. These imports act as modules that encapsulate specific functionalities or utilities.

- [SDK Imports](/Studio_UI/SDK_Overview/SDK_Imports.md): The Pimcore Studio UI SDK provides a set of predefined imports that developers can use to build and extend functionalities. These imports act as modules that encapsulate specific functionalities or utilities.

##### Services_and_Dependency_Injection

Services are straightforward objects that assist with specific tasks. To maximize the benefits of services, weâ€™ve introduced a service container using Inversify.

- [Services and Dependency Injection](/Studio_UI/SDK_Overview/Services_and_Dependency_Injection.md): Services are straightforward objects that assist with specific tasks. To maximize the benefits of services, weâ€™ve introduced a service container using Inversify.

##### UI_Components_and_Storybook

Pimcore Studio UI offers React components to simplify your work.

- [UI Components and Storybook](/Studio_UI/SDK_Overview/UI_Components_and_Storybook.md): Pimcore Studio UI offers React components to simplify your work.

##### Widget_Manager

The Widget Manager is a powerful tool that allows you to manage and interact with widgets in the Pimcore Studio UI.

- [Widget Manager](/Studio_UI/SDK_Overview/Widget_Manager.md): The Widget Manager is a powerful tool that allows you to manage and interact with widgets in the Pimcore Studio UI.

#### Studio_UI_Core_Development

How to install

- [Studio UI Core Development](/Studio_UI/Studio_UI_Core_Development.md): How to install

### Targeting

Pimcore provides a very powerful integrated behavioral targeting and personalization engine. With this toolkit, it is possible to profile visitors based on their behavior, assign target groups to them and provide personalized content to

- [Behavioral Targeting and Personalization](/Targeting.md): Pimcore provides a very powerful integrated behavioral targeting and personalization engine. With this toolkit, it is possible to profile visitors based on their behavior, assign target groups to them and provide personalized content to

#### Development_Documentation

The following section describes the technical concepts and aspects of the Pimcore targeting engine. For usage

- [Development Documentation](/Targeting/Development_Documentation.md): The following section describes the technical concepts and aspects of the Pimcore targeting engine. For usage

##### Action_Handlers

After a targeting rule matched it executes one or more actions as configured in the admin UI. These actions are actually

- [Action Handlers](/Targeting/Development_Documentation/Action_Handlers.md): After a targeting rule matched it executes one or more actions as configured in the admin UI. These actions are actually

##### Conditions

Conditions are logical blocks which can be combined with other conditions inside a target rule. A condition is expected

- [Conditions](/Targeting/Development_Documentation/Conditions.md): Conditions are logical blocks which can be combined with other conditions inside a target rule. A condition is expected

##### Data_Providers

A data provider is a service implementing the DataProviderInterface.

- [Data Providers](/Targeting/Development_Documentation/Data_Providers.md): A data provider is a service implementing the DataProviderInterface.

##### Frontend_Javascript

When targeting is enabled, a snippet of JavaScript in injected into every response. This snippet contains some information

- [Frontend JavaScript](/Targeting/Development_Documentation/Frontend_Javascript.md): When targeting is enabled, a snippet of JavaScript in injected into every response. This snippet contains some information

##### Targeting_Storage

[TOC]

- [Targeting Storage](/Targeting/Development_Documentation/Targeting_Storage.md): [TOC]

##### Visitor_Info

The VisitorInfo is the central data object which is passed to every part of the targeting system. A new VisitorInfo

- [Visitor Info](/Targeting/Development_Documentation/Visitor_Info.md): The VisitorInfo is the central data object which is passed to every part of the targeting system. A new VisitorInfo

#### Installation

Installation Process

- [Installation](/Targeting/Installation.md): Installation Process

##### Updating_from_Version_1.x_to_2.x

The library geoip2/geoip2 was updated from version 2.x to 3.x.

- [Updating from Version 1.x to 2.x](/Targeting/Installation/Updating_from_Version_1.x_to_2.x.md): The library geoip2/geoip2 was updated from version 2.x to 3.x.

#### Usage


##### Concepts

The following core concepts are the essential parts of Pimcores personalization engine.

- [Pimcore Concepts for Personalization](/Targeting/Usage/Concepts.md): The following core concepts are the essential parts of Pimcores personalization engine.

##### Examples

The following pages show a few examples to better understand how things can be achieved with the Pimcore targeting

- [Examples for Personalization and Targeting](/Targeting/Usage/Examples.md): The following pages show a few examples to better understand how things can be achieved with the Pimcore targeting

##### How_to_Personalize_Content

On the following pages you find a short tutorial for how to personalize content.

- [How to Personalize Content](/Targeting/Usage/How_to_Personalize_Content.md): On the following pages you find a short tutorial for how to personalize content.
- [Create Personalized Content](/Targeting/Usage/How_to_Personalize_Content/Create_Personalized_Content.md): The simplest way to create personalized content is with Pimcore documents. They provide the possibility to create
- [Define Target Groups](/Targeting/Usage/How_to_Personalize_Content/Define_Target_Groups.md): It is recommended to group your visitors into target groups. They help you to keep track and have an overview about what
- [Deliver Personalized Content](/Targeting/Usage/How_to_Personalize_Content/Deliver_Personalized_Content_and_Debug.md): Once target groups are defined, visitor profiling rules are set and personalized content is created, it is time to
- [Visitor Profiling](/Targeting/Usage/How_to_Personalize_Content/Visitor_Profiling.md): Visitor profiling is the process of getting to know visitors and assign one or multiple target groups based on their

### TinyMCE_WYSWIYG_Editor

General

- [TinyMCE WSYIWYG editor](/TinyMCE_WYSWIYG_Editor.md): General

#### Global_Configuration_Admin_Ui

You can add a Global Configuration for all WYSIWYG Editors for all documents by setting pimcore.document.editables.wysiwyg.defaultEditorConfig.

- [Global Configuration in Admin UI Classic](/TinyMCE_WYSWIYG_Editor/Global_Configuration_Admin_Ui.md): You can add a Global Configuration for all WYSIWYG Editors for all documents by setting pimcore.document.editables.wysiwyg.defaultEditorConfig.

#### Global_Configuration_Studio_Ui

You can add a Global Configuration with the symfony config from studio-ui-bundle

- [Global Configuration in Studio UI](/TinyMCE_WYSWIYG_Editor/Global_Configuration_Studio_Ui.md): You can add a Global Configuration with the symfony config from studio-ui-bundle

#### Installation

Make sure the bundle is enabled in the config/bundles.php file. The following lines should be added:

- [Installation](/TinyMCE_WYSWIYG_Editor/Installation.md): Make sure the bundle is enabled in the config/bundles.php file. The following lines should be added:

### Translation_Provider_Interfaces

This bundle allows you to create translation jobs, which are sent to external APIs for further processing. The processed jobs will then be fetched again in order to update any supported elements (see the list below).

- [Pimcore Translation Provider Interfaces](/Translation_Provider_Interfaces.md): This bundle allows you to create translation jobs, which are sent to external APIs for further processing. The processed jobs will then be fetched again in order to update any supported elements (see the list below).

#### DeepL

Setup

- [DeepL](/Translation_Provider_Interfaces/DeepL.md): Setup

#### Development_Details

General Aspects

- [Development Details](/Translation_Provider_Interfaces/Development_Details.md): General Aspects

##### Database_Model

| Table                                       | Description                                                                                                                                     |

- [Database Model](/Translation_Provider_Interfaces/Development_Details/Database_Model.md): | Table                                       | Description                                                                                                                                     |

#### Installation_and_Configuration

Installation Process

- [Installation and Configuration](/Translation_Provider_Interfaces/Installation_and_Configuration.md): Installation Process

##### Commands

Important Commands

- [Commands](/Translation_Provider_Interfaces/Installation_and_Configuration/Commands.md): Important Commands

##### Configuration

Configuration takes places in configuration files or can be applied directly in Pimcore

- [Configuration](/Translation_Provider_Interfaces/Installation_and_Configuration/Configuration.md): Configuration takes places in configuration files or can be applied directly in Pimcore

##### Upgrade

Update to Version 4.0

- [Update Notes](/Translation_Provider_Interfaces/Installation_and_Configuration/Upgrade.md): Update to Version 4.0

#### Translation_Workflow

Collecting

- [Translation Workflow](/Translation_Provider_Interfaces/Translation_Workflow.md): Collecting

##### Automatic_Change_Detection

Automatic change detection consists of two parts:

- [Automatic Change Detection](/Translation_Provider_Interfaces/Translation_Workflow/Automatic_Change_Detection.md): Automatic change detection consists of two parts:

##### Manual_Trigger

Besides automatic change detection, it is also possible to add items to the translation queue manually.

- [Manual Trigger](/Translation_Provider_Interfaces/Translation_Workflow/Manual_Trigger.md): Besides automatic change detection, it is also possible to add items to the translation queue manually.

#### Translation.com

Following information apply to translation.com adapter.

- [Translation.com](/Translation_Provider_Interfaces/Translation.com.md): Following information apply to translation.com adapter.

#### Translations_Jobs_Dashboard

Dashboard to see all translation jobs and manually trigger workflow transitions of single jobs.

- [Translation Jobs Dashboard](/Translation_Provider_Interfaces/Translations_Jobs_Dashboard.md): Dashboard to see all translation jobs and manually trigger workflow transitions of single jobs.

### Web_To_Print

Adds the ability to create web-to-print documents in Pimcore and to convert them into a PDF.

- [Pimcore Web to Print Module](/Web_To_Print.md): Adds the ability to create web-to-print documents in Pimcore and to convert them into a PDF.

#### Doc_Types_and_Available_Processors

Document Types

- [Document Types and Available PDF Processors](/Web_To_Print/Doc_Types_and_Available_Processors.md): Document Types

#### Installation

Installing processor dependencies

- [Installation](/Web_To_Print/Installation.md): Installing processor dependencies

##### Upgrade

Following steps are necessary during updating to newer versions.

- [Upgrade Information](/Web_To_Print/Installation/Upgrade.md): Following steps are necessary during updating to newer versions.

#### Print_Documents

Print documents are the way to create print-ready PDFs directly within Pimcore.

- [Print Documents](/Web_To_Print/Print_Documents.md): Print documents are the way to create print-ready PDFs directly within Pimcore.

#### Web2Print_Extending_Config_for_PDFX_conformance

Sometimes it is necessary to add additional configuration options to the PDF processing configuration in the Pimcore backend UI -

- [Extending PDF Creation Config for PDF/X Conformance](/Web_To_Print/Web2Print_Extending_Config_for_PDFX_conformance.md): Sometimes it is necessary to add additional configuration options to the PDF processing configuration in the Pimcore backend UI -

### Worfklow_Designer

The Workflow Designer extension provides a visual designer for

- [Pimcore Workflow Designer](/Worfklow_Designer.md): The Workflow Designer extension provides a visual designer for

#### Installation_and_Configuration

Bundle Installation

- [Installation und Configuration](/Worfklow_Designer/Installation_and_Configuration.md): Bundle Installation

#### Technical_Aspects

Workflow Definition Persistence

- [Technical Aspects](/Worfklow_Designer/Technical_Aspects.md): Workflow Definition Persistence

#### Upgrade

Update to Version 1.4

- [Update Notes](/Worfklow_Designer/Upgrade.md): Update to Version 1.4

#### Workflow_Configuration

The Workflow Designer provides a workflow list with folder structured listing of workflows to

- [Workflow Configuration](/Worfklow_Designer/Workflow_Configuration.md): The Workflow Designer provides a workflow list with folder structured listing of workflows to

##### Workflow_Configuration_Editor

The workflow configuration editor allows to configure the workflow. Beside some general settings, the places, transitions

- [Workflow Configuration Editor](/Worfklow_Designer/Workflow_Configuration/Workflow_Configuration_Editor.md): The workflow configuration editor allows to configure the workflow. Beside some general settings, the places, transitions

##### Workflow_List

The workflow list shows all workflows defined with the workflow designer in a folder based structure. From there it is

- [Workflow List](/Worfklow_Designer/Workflow_Configuration/Workflow_List.md): The workflow list shows all workflows defined with the workflow designer in a folder based structure. From there it is

### Workflow_Automation

The Workflow Automation Integration bundle allows you to export configuration blueprints to workflow automation engines like n8n based on existing Datahub configurations for GraphQL and Webhooks. These engines, designed to define and execute successions of tasks, also offer a good solution to speed up the integration of Pimcore with any third-party system.

- [Workflow Automation Integration](/Workflow_Automation.md): The Workflow Automation Integration bundle allows you to export configuration blueprints to workflow automation engines like n8n based on existing Datahub configurations for GraphQL and Webhooks. These engines, designed to define and execute successions of tasks, also offer a good solution to speed up the integration of Pimcore with any third-party system.

#### Installation

This bundle is only supported on Pimcore Core Framework 11.

- [Installation of the Workflow Automation Integration Bundle](/Workflow_Automation/Installation.md): This bundle is only supported on Pimcore Core Framework 11.

#### Work_with_WAI

The Workflow Automation Integration functionality can be accessed from the sidebar menu in the Tools > Automation Blueprints section.

- [Work with Workflow Automation Integration](/Workflow_Automation/Work_with_WAI.md): The Workflow Automation Integration functionality can be accessed from the sidebar menu in the Tools > Automation Blueprints section.