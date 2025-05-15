# ECS Task Definition for Laravel app
resource "aws_ecs_task_definition" "laravel_app" {
  family                   = "lab-laravel-task" # task family name
  cpu                      = "1024"             # 1/4 vCPU
  memory                   = "512"              # 0.5 GB
  network_mode             = "bridge"
  requires_compatibilities = ["EC2"]
  execution_role_arn       = data.aws_iam_role.lab_role.arn
  task_role_arn            = data.aws_iam_role.lab_role.arn
  track_latest             = true
  container_definitions = jsonencode([
    {
      name      = "final-app"
      image     = "ghcr.io/nthu-cp-01/final:main"
      essential = true
      healthCheck = {
        command     = ["CMD-SHELL", "curl -f http://localhost/up || exit 1"]
        interval    = 300
        timeout     = 5
        retries     = 3
        startPeriod = 120
      }
      portMappings = [
        {
          containerPort = 80
          hostPort      = 80
        }
      ]
      environment = [
        # Application
        { name = "APP_NAME", value = "AWS" },
        { name = "APP_ENV", value = "production" },
        { name = "APP_KEY", value = "base64:3vVjC2JIukr6/8WQrd3cxYsdn4DKNn35h3/t/uIaG5M=" },
        { name = "APP_URL", value = "http://localhost" },
        { name = "APP_DEBUG", value = "true" },
        { name = "APP_LOCALE", value = "en" },
        { name = "APP_FALLBACK_LOCALE", value = "en" },
        { name = "APP_TIMEZONE", value = "UTC+8" },

        # Database
        { name = "DB_CONNECTION", value = "pgsql" },
        { name = "DB_HOST", value = aws_db_instance.lab_rds_instance.address },
        { name = "DB_PORT", value = tostring(aws_db_instance.lab_rds_instance.port) },
        { name = "DB_DATABASE", value = aws_db_instance.lab_rds_instance.db_name },
        { name = "DB_USERNAME", value = aws_db_instance.lab_rds_instance.username },
        { name = "DB_PASSWORD", value = "cppassword" },

        # Cache
        { name = "REDIS_CLIENT", value = "predis" },
        { name = "REDIS_HOST", value = aws_elasticache_replication_group.lab_redis.primary_endpoint_address },
        { name = "REDIS_PORT", value = "6379" },
        { name = "REDIS_USERNAME", value = "default" },
        { name = "REDIS_PASSWORD", value = "default" },
        { name = "CACHE_DRIVER", value = "redis" },
        { name = "QUEUE_CONNECTION", value = "redis" },

        # Session
        { name = "SESSION_DRIVER", value = "redis" },
        { name = "SESSION_CONNECTION", value = "default" },
        { name = "SESSION_LIFETIME", value = "120" },
        { name = "SESSION_ENCRYPT", value = "false" },
        { name = "SESSION_PATH", value = "/" },
        { name = "SESSION_DOMAIN", value = "null" },

        # Storage(swap with S3 maybe?)
        { name = "FILESYSTEM_DISK", value = "local" },

        # Cognito
        { name = "COGNITO_HOST", value = format(
          "https://%s.auth.%s.amazoncognito.com",
          aws_cognito_user_pool_domain.lab_user_pool_domain.domain,
          "us-east-1"
        ) },
        { name = "COGNITO_CLIENT_ID", value = aws_cognito_user_pool_client.lab_user_pool_client.id },
        { name = "COGNITO_CLIENT_SECRET", value = aws_cognito_user_pool_client.lab_user_pool_client.client_secret },
        { name = "COGNITO_LOGIN_SCOPE", value = "openid,profile,email" },
        { name = "COGNITO_CALLBACK_URL", value = format("https://%s/login/cognito/callback", aws_lb.lab_app_lb.dns_name) },
        { name = "COGNITO_SIGN_OUT_URL", value = format("https://%s/login", aws_lb.lab_app_lb.dns_name) },
      ]
      logConfiguration = {
        logDriver = "awslogs",
        options = {
          "awslogs-region"        = "us-east-1",
          "awslogs-group"         = aws_cloudwatch_log_group.app_logs.name,
          "awslogs-stream-prefix" = "laravel"
        }
      }
    }
  ])
  runtime_platform {
    operating_system_family = "LINUX"
    cpu_architecture        = "X86_64"
  }
}

# ECS Service to run the Laravel task
resource "aws_ecs_service" "web_service" {
  name                   = "lab-final-laravel-service"
  cluster                = aws_ecs_cluster.lab_app_cluster.id
  task_definition        = aws_ecs_task_definition.laravel_app.arn
  enable_execute_command = true
  desired_count          = 1

  capacity_provider_strategy {
    capacity_provider = aws_ecs_capacity_provider.lab_app_ec2_capacity_provider.name
    weight            = 1
  }

  load_balancer {
    target_group_arn = aws_lb_target_group.lab_app_tg.arn
    container_name   = "final-app"
    container_port   = 80
  }

  depends_on = [aws_lb_listener.lab_app_lb_listener]
}
