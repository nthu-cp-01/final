# Generate a new RSA private key for the TLS certificate (Terraform TLS provider)
resource "tls_private_key" "cert_rsa" {
  algorithm = "RSA"
  rsa_bits  = 4096
}

# Create a self-signed TLS certificate using the private key
resource "tls_self_signed_cert" "cert_rsa" {
  private_key_pem   = tls_private_key.cert_rsa.private_key_pem
  is_ca_certificate = true
  subject {
    country             = "TW"
    locality            = "Hsinchu"
    organization        = "NTHU"
    organizational_unit = "Cloud Programming Group 01 Inc."
    common_name         = aws_lb.lab_app_lb.dns_name
  }
  validity_period_hours = 8760 # 1 year validity
  allowed_uses = [
    "any_extended",
    "server_auth",
    "key_encipherment",
    "cert_signing",
    "digital_signature",
    "crl_signing"
  ]
}

# Create an ACM certificate using the self-signed certificate
resource "aws_acm_certificate" "app_cert_rsa" {
  certificate_body = tls_self_signed_cert.cert_rsa.cert_pem
  private_key      = tls_private_key.cert_rsa.private_key_pem
  tags = {
    Name = "lab-app-cert-rsa"
  }
}
