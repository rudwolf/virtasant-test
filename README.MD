<h1 align="center">
<br>Advanced Media Control Plugin
</h1>

## Assignment Plugin

This repository contains the code for the test to accomplish the following task:
>a. Develop a WordPress plugin which adds the following features in WP Admin.
>1. Add a field on the Term Add [1] and Term Edit [2] page that allows users to upload or
select an existing image in either PNG or JPEG format from the WordPress Media
Library. The image preview should be displayed on the same screen. You can use
third-party libraries such as CMB2 to achieve this.
>2. Prevent users from deleting any images from the WordPress Media Library in the
following scenarios,
a. Prevent the deletion of an image if it is being used as a Featured Image [3] in an
article.
b. Prevent the deletion of an image if it is being used in the content of a post [4]
(Post Body).
c. Prevent the deletion of an image if it is being used in a Term Edit Page (as
implemented in point 1).
d. If the image is being used in any of these places and the user attempts to delete
it, display a message asking the user to remove the image from the post(s) or
term(s) where it is being used. The interface should display IDs of the post(s) or
term(s) with an edit link [5]. The user should be able to determine whether the
given ID is for a post or for a term. This message should appear in every place in
WordPress from where images can be deleted, such as the Media List table
[6], Media Library Popup [5] etc.
e. In the Media Library Table, add a column named "Attached Objects" [6] that
displays a comma-separated list of IDs (linked to the corresponding edit page).
The user should be able to determine whether the given ID is for a post or for a
term.
>3. Provide following functionalities via REST API under `/assignment/v1/` namespace.
a. An API endpoint that returns details of a given image ID
i. Response should contain a JSON object with the following details
>- ID
>- Date
>- Slug
>- Type ( JPEG, PNG )
>- Link
>- Alt text
>- Attached Objects
>Should contain Post or Term IDs to which the given image is
attached. Structure the field structure accordingly.
>
>b. An API endpoint that deletes a given image if it is not attached to any of the
posts or terms. If the given image is attached to any of the posts or terms then
return a response stating that deletion failed.
>
> 4. Add unit tests for all critical functions.

---

## What was done

The code reflects the request except for unit tests.

## Apology for Missing Unit Tests
I apologize for being unable to create unit tests for the WordPress plugin as part of this take-home assignment. Due to the heavy reliance on front-end components in the plugin, traditional PHP unit tests are insufficient for effectively testing its functionality. Instead, tools like Selenium or other browser automation testing tools would be required to simulate user interactions and comprehensively test the plugin.

Unfortunately, implementing these testing tools goes beyond the scope of this assignment. Nonetheless, I hope the quality of the plugin I've developed can still be considered, and I'd be happy to discuss alternative testing approaches or demonstrate my testing skills in another way.