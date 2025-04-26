output "sns_topic_arn" {
  description = "The ARN of the SNS topic for billing alerts"
  value       = aws_sns_topic.billing_alerts.arn
}

output "alarm_arns" {
  description = "The ARNs of the created CloudWatch billing alarms"
  value       = aws_cloudwatch_metric_alarm.billing_alarms[*].arn
}