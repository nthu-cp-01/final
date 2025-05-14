resource "aws_cognito_user_pool" "lab_user_pool" {
  name = "the_final"

  alias_attributes = ["email"]
}

resource "aws_cognito_user_pool_domain" "lab_user_pool_domain" {
  user_pool_id          = aws_cognito_user_pool.lab_user_pool.id
  domain                = "cloud-programming-auth"
  managed_login_version = 1
}

resource "aws_cognito_user_pool_client" "lab_user_pool_client" {
  name         = "the_final_app_client"
  user_pool_id = aws_cognito_user_pool.lab_user_pool.id

  allowed_oauth_flows_user_pool_client = true
  allowed_oauth_flows                  = ["implicit", "code"]
  allowed_oauth_scopes                 = ["email", "openid", "profile"]
  supported_identity_providers         = ["COGNITO"]
  generate_secret                      = true

  explicit_auth_flows = [
    "ALLOW_USER_PASSWORD_AUTH",
    "ALLOW_USER_SRP_AUTH",
    "ALLOW_REFRESH_TOKEN_AUTH",
  ]

  logout_urls = [
    "https://final.test/login",
  ]
  callback_urls = [
    "https://final.test/login/cognito/callback",
  ]
}

resource "aws_cognito_resource_server" "lab_resource_server" {
  name         = "final_app_resource_server"
  user_pool_id = aws_cognito_user_pool.lab_user_pool.id
  identifier   = "https://final.test"
}

resource "aws_cognito_user" "lab_cognito_user" {
  user_pool_id = aws_cognito_user_pool.lab_user_pool.id

  username = "frank"
  password = "!CoffeeIsGreat34"

  attributes = {
    name           = "Frank"
    email          = "frank@test.com"
    email_verified = true
  }
}

resource "aws_cognito_user" "lab_cognito_user_test" {
  user_pool_id = aws_cognito_user_pool.lab_user_pool.id

  username = "test"
  password = "Aoeuaoeu_123"

  attributes = {
    name           = "Test User"
    email          = "test@test.com"
    email_verified = true
  }
}
