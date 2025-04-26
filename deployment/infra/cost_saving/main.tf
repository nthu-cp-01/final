# Create SNS topic for billing notifications
resource "aws_sns_topic" "billing_alerts" {
  name = "billing-alerts-topic"
}

# Set up email subscriptions for the SNS topic
resource "aws_sns_topic_subscription" "email_subscriptions" {
  count     = length(var.notification_emails)
  topic_arn = aws_sns_topic.billing_alerts.arn
  protocol  = "email"
  endpoint  = var.notification_emails[count.index]
}

# Create CloudWatch metric alarms for each billing threshold
resource "aws_cloudwatch_metric_alarm" "billing_alarms" {
  count               = length(var.billing_thresholds)
  alarm_name          = "billing-alarm-${var.billing_thresholds[count.index]}-usd"
  comparison_operator = "GreaterThanOrEqualToThreshold"
  evaluation_periods  = 1
  metric_name         = "EstimatedCharges"
  namespace           = "AWS/Billing"
  period              = 60  # 1 minute
  statistic           = "Maximum"
  threshold           = var.billing_thresholds[count.index]
  alarm_description   = "Billing alarm when charges exceed $${var.billing_thresholds[count.index]}"
  alarm_actions       = [aws_sns_topic.billing_alerts.arn]
  
  dimensions = {
    Currency = "USD"
  }
}