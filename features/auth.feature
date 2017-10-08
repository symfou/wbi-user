Feature: Race
    In order to use the application
    I need to be able to create and update races trough the API.

    background:
    Given I generate client with:
    | name | Store |
    | redirectUri | http://majdi.dev/ |
    | grantType | token |


    Scenario: Auth api is secured
#        When I send a "GET" request to "/api/v1/user/validities/check"
#        Then print current URL
#        Then the response status code should be 401
#        And the response should be in JSON
#        And the header "Content-Type" should be equal to "application/json"

        Given I prepare a GET request on "/api/v1/user/validities/check"
        Then    I print last URL requested
        Given I specified the following request headers:
            | Accept | application/json, text/plain, */* |
        When  I send the request
        #        Then print current URL
        Then  I should receive a 401 response

#    Scenario: login page
#        Given I load all fixtures
#        When I am on "/login"
#        Then print current URL
#        And I should see "Sign in to Blur Admin"
#        When I fill in the following:
#            | _username | admin |
#            | _password | admin |
#        When I press "submit"
#        Then the response status code should be 200
#        And I should be on "/admin/select-app"
#        And I should see "Select app"
#
#    Scenario: Authenticate
#        When I try authenticate in "/admin/oauth/v2/auth"
#        Then print current URL
#        Then print last JSON response
