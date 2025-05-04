# **Project Requirements Document: The final project prd**


## 1. Models
- User (builtin, don't touch)
- Location (FR00)
  - id (int autoinc, unique identifier for the location, primary key)
  - name (string, name of the location)
  - description (string, description of the location)
  - deviceId (string, unique identifier for the IoT device associated with the location)
  - use timestamps and softdeletes in migration and model.

## 2. Functional Requirements
The following table outlines the detailed functional requirements of the final project website.

| Requirement ID | Description               | User Story                                                                                       | Expected Behavior/Outcome                                                                                                     |
|-----------------|---------------------------|--------------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------|
| FR00          | Manage Locations (IoT Devices registered on AWS IoT Core) and necessary information required to access the corresponding device shadow properties. Each IoT Device correspond to an unique location.   | As a user, I want to be able to manage Locations(IoT devices) to be tracked in the system that are registered on AWS IoT Core.              | The system should provide a clear interface for managing Locations and display attributes fetched from device shadows properly. |
| FR00-1          | Create a new Location   | As a user, I want to be able to create a new Location.              | The system should provide a clear way for adding an location for tracking, possibly through a modal or slide-out form, triggered by a "Create" button. |
| FR00-2          | List all Locations.   | As a user, I want to be able to list all Locations that are being tracked in the system.              | The system should provide a clear view that list out all Locations and its related device shadow attributes. |
| FR00-3          | Modify the selected Location  | As a user, I want to be able to modify the selected Locations from the list of tracking devices in system.              | The system should provide a clear way for modifying attributes that are used to access the corresponding device shadow properties.|
| FR00-4          | Remove the selected AWS IoT Device   | As a user, I want to be able to remove the selected Location from the system.              | The system should provide a clear way for removing a selected Location from tracking.|
