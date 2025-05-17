resource "aws_cognito_user_pool" "lab_user_pool" {
  name = "the_final"

  username_attributes      = ["email"]
  auto_verified_attributes = ["email"]

  schema {
    name                = "name"
    required            = true
    attribute_data_type = "String"

    string_attribute_constraints {
      min_length = 1
      max_length = 255
    }
  }

  username_configuration {
    case_sensitive = true
  }

  admin_create_user_config {
    allow_admin_create_user_only = false
  }
  password_policy {
    minimum_length    = 8
    require_uppercase = false
    require_lowercase = false
    require_numbers   = false
    require_symbols   = false
  }
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
    format(
      "https://%s/login",
      aws_lb.lab_app_lb.dns_name
    ),
  ]
  callback_urls = [
    format(
      "https://%s/login/cognito/callback",
      aws_lb.lab_app_lb.dns_name
    ),
  ]
}

resource "aws_cognito_resource_server" "lab_resource_server" {
  name         = "final_app_resource_server"
  user_pool_id = aws_cognito_user_pool.lab_user_pool.id
  identifier   = format("https://%s", aws_lb.lab_app_lb.dns_name)
}
