# ECS Cluster for the Laravel app
resource "aws_ecs_cluster" "lab_app_cluster" {
  name = "lab-app-cluster"
}
resource "aws_ecs_cluster_capacity_providers" "lab_app_cluster_capacity_providers" {
  cluster_name = aws_ecs_cluster.lab_app_cluster.name

  capacity_providers = ["FARGATE", "FARGATE_SPOT"]

  default_capacity_provider_strategy {
    base              = 0
    weight            = 1
    capacity_provider = "FARGATE"
  }
  default_capacity_provider_strategy {
    base              = 0
    weight            = 1
    capacity_provider = "FARGATE_SPOT"
  }
}

# CloudWatch Log Group for container logs
resource "aws_cloudwatch_log_group" "app_logs" {
  name              = "/ecs/lab-final"
  retention_in_days = 14 # retain logs for 2 weeks (adjust as needed)
}

# ECS Task Definition for Laravel app
resource "aws_ecs_task_definition" "laravel_app" {
  family                   = "lab-laravel-task" # task family name
  cpu                      = "256"              # 1/4 vCPU
  memory                   = "512"              # 0.5 GB
  network_mode             = "awsvpc"
  requires_compatibilities = ["FARGATE"]
  execution_role_arn       = data.aws_iam_role.lab_role.arn
  task_role_arn            = data.aws_iam_role.lab_role.arn
  container_definitions = jsonencode([
    {
      name      = "final-app"
      image     = "ghcr.io/nthu-cp-01/final:main"
      essential = true
      portMappings = [
        {
          containerPort = 80
          hostPort      = 80
        }
      ]
      environment = [
        # Application
        { name = "APP_NAME", value = "AWS" },
        { name = "APP_ENV", value = "local" },
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
        { name = "DB_DATABASE", value = "cp_final" },
        { name = "DB_USERNAME", value = "cpuser" },
        { name = "DB_PASSWORD", value = "cppassword" },

        # Cache
        { name = "REDIS_CLIENT", value = "phpredis" },
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
  launch_type            = "FARGATE"
  platform_version       = "1.4.0" # latest Fargate platform (supports secrets fetch, etc.)
  desired_count          = 1

  network_configuration {
    subnets = [
      aws_subnet.lab_private_subnet_a.id,
      aws_subnet.lab_private_subnet_b.id
    ]

    security_groups  = [aws_security_group.ecs_sg.id]
    assign_public_ip = false
  }

  load_balancer {
    target_group_arn = aws_lb_target_group.lab_app_tg.arn
    container_name   = "final-app"
    container_port   = 80
  }

  depends_on = [aws_lb_listener.lab_app_lb_listener]
}


