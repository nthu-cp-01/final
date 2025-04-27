variable "billing_thresholds" {
  description = "List of billing threshold values in USD to create alarms for"
  type        = list(number)
}

variable "notification_emails" {
  description = "List of email addresses to send billing notifications to"
  type        = list(string)
}